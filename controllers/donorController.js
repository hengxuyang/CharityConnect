const { User, Charity, Request, Donation, Item, Category } = require('../models');
const { Op } = require('sequelize');
const { v4: uuidv4 } = require('uuid');
const notificationService = require('../utils/notification');

// Donor dashboard
exports.getDashboard = async (req, res) => {
  try {
    // Get counts for dashboard
    const [
      totalDonations,
      pendingDonations,
      deliveredDonations,
      availableRequests
    ] = await Promise.all([
      Donation.count({ where: { donor_id: req.user.id } }),
      Donation.count({ where: { donor_id: req.user.id, status: 'Pending' } }),
      Donation.count({ where: { donor_id: req.user.id, status: 'Delivered' } }),
      Request.count({ where: { status: 'Pending' } })
    ]);
    
    // Get recent donations
    const recentDonations = await Donation.findAll({
      where: { donor_id: req.user.id },
      include: [
        {
          model: Request,
          as: 'request',
          include: [
            { model: Item, as: 'item' },
            { model: Charity, as: 'charity' }
          ]
        }
      ],
      order: [['created_at', 'DESC']],
      limit: 5
    });
    
    // Get recent requests
    const recentRequests = await Request.findAll({
      where: { status: 'Pending' },
      include: [
        { model: Item, as: 'item' },
        { model: Charity, as: 'charity' }
      ],
      order: [['created_at', 'DESC']],
      limit: 5
    });
    
    res.render('donor/dashboard', {
      title: 'Donor Dashboard',
      user: req.user,
      stats: {
        totalDonations,
        pendingDonations,
        deliveredDonations,
        availableRequests
      },
      recentDonations,
      recentRequests
    });
  } catch (error) {
    console.error('Donor dashboard error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading the dashboard',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// Browse donation requests
exports.browseRequests = async (req, res) => {
  try {
    const { category, charity, request_type, search } = req.query;
    
    // Build query conditions
    const whereConditions = { status: 'Pending' };
    const charityConditions = {};
    
    if (request_type && ['drop-off', 'pickup'].includes(request_type)) {
      whereConditions.request_type = request_type;
    }
    
    if (charity) {
      whereConditions.charity_id = charity;
    }
    
    if (search) {
      charityConditions.name = { [Op.like]: `%${search}%` };
    }
    
    // Get requests
    const requests = await Request.findAll({
      where: whereConditions,
      include: [
        { 
          model: Item, 
          as: 'item',
          include: [
            { model: Category, as: 'category' }
          ]
        },
        { 
          model: Charity, 
          as: 'charity',
          where: charityConditions
        }
      ],
      order: [['created_at', 'DESC']]
    });
    
    // Filter by category if specified
    const filteredRequests = category 
      ? requests.filter(request => request.item.category_id === parseInt(category))
      : requests;
    
    // Get categories for filter
    const categories = await Category.findAll({
      order: [['name', 'ASC']]
    });
    
    // Get charities for filter
    const charities = await Charity.findAll({
      where: { status: 'approved' },
      order: [['name', 'ASC']]
    });
    
    res.render('donor/browse-requests', {
      title: 'Browse Donation Requests',
      user: req.user,
      requests: filteredRequests,
      categories,
      charities,
      filters: { category, charity, request_type, search }
    });
  } catch (error) {
    console.error('Browse requests error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading requests',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// View request details
exports.viewRequest = async (req, res) => {
  try {
    const { id } = req.params;
    
    // Get request
    const request = await Request.findOne({
      where: { id, status: 'Pending' },
      include: [
        { 
          model: Item, 
          as: 'item',
          include: [
            { model: Category, as: 'category' }
          ]
        },
        { model: Charity, as: 'charity' }
      ]
    });
    
    if (!request) {
      return res.status(404).render('error', {
        title: 'Error',
        message: 'Request not found or no longer available',
        error: {}
      });
    }
    
    // Check if user has already committed to this request
    const existingDonation = await Donation.findOne({
      where: { 
        donor_id: req.user.id,
        request_id: id
      }
    });
    
    // Get total committed donations for this request
    const totalCommitted = await Donation.sum('quantity', {
      where: { request_id: id }
    }) || 0;
    
    // Calculate remaining quantity needed
    const remainingQuantity = Math.max(0, request.quantity - totalCommitted);
    
    res.render('donor/request-details', {
      title: 'Request Details',
      user: req.user,
      request,
      existingDonation,
      totalCommitted,
      remainingQuantity
    });
  } catch (error) {
    console.error('View request error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading request details',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// Make donation
exports.makeDonation = async (req, res) => {
  try {
    const { id } = req.params;
    const { quantity } = req.body;
    
    // Get request
    const request = await Request.findOne({
      where: { id, status: 'Pending' },
      include: [
        { model: Item, as: 'item' },
        { model: Charity, as: 'charity' }
      ]
    });
    
    if (!request) {
      return res.status(404).json({ success: false, message: 'Request not found or no longer available' });
    }
    
    // Check if user has already committed to this request
    const existingDonation = await Donation.findOne({
      where: { 
        donor_id: req.user.id,
        request_id: id
      }
    });
    
    if (existingDonation) {
      return res.status(400).json({ success: false, message: 'You have already committed to this request' });
    }
    
    // Get total committed donations for this request
    const totalCommitted = await Donation.sum('quantity', {
      where: { request_id: id }
    }) || 0;
    
    // Calculate remaining quantity needed
    const remainingQuantity = Math.max(0, request.quantity - totalCommitted);
    
    // Check if donation quantity is valid
    if (quantity <= 0 || quantity > remainingQuantity) {
      return res.status(400).json({ 
        success: false, 
        message: `Invalid quantity. Please enter a value between 1 and ${remainingQuantity}` 
      });
    }
    
    // Create donation
    const donation = await Donation.create({
      id: uuidv4(),
      donor_id: req.user.id,
      request_id: id,
      quantity,
      status: 'Pending',
      created_at: new Date()
    });
    
    // Notify charity
    const charityUsers = await User.findAll({
      where: { 
        charity_id: request.charity_id,
        role: 'charity'
      }
    });
    
    if (charityUsers && charityUsers.length > 0) {
      const message = `${req.user.name} has committed to donate ${quantity} ${request.item.unit}(s) of ${request.item.name} to your request.`;
      
      for (const user of charityUsers) {
        await notificationService.notifyRequestUpdate(
          user.id,
          request.id,
          message
        );
      }
    }
    
    // Check if request is now fully committed
    const newTotalCommitted = totalCommitted + parseInt(quantity);
    
    if (newTotalCommitted >= request.quantity) {
      // Update request status to indicate it's fully committed
      // Note: We don't mark it as Fulfilled yet, as the donations haven't been delivered
      // This is just for UI indication
      console.log('Request fully committed:', request.id);
    }
    
    return res.status(201).json({ success: true, message: 'Donation committed successfully' });
  } catch (error) {
    console.error('Make donation error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while processing your donation' });
  }
};

// View my donations
exports.viewMyDonations = async (req, res) => {
  try {
    const { status } = req.query;
    
    // Build query conditions
    const whereConditions = { donor_id: req.user.id };
    
    if (status && ['Pending', 'Delivered', 'Cancelled'].includes(status)) {
      whereConditions.status = status;
    }
    
    // Get donations
    const donations = await Donation.findAll({
      where: whereConditions,
      include: [
        {
          model: Request,
          as: 'request',
          include: [
            { model: Item, as: 'item' },
            { model: Charity, as: 'charity' }
          ]
        }
      ],
      order: [['created_at', 'DESC']]
    });
    
    res.render('donor/my-donations', {
      title: 'My Donations',
      user: req.user,
      donations,
      filters: { status }
    });
  } catch (error) {
    console.error('View my donations error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading your donations',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// Cancel donation
exports.cancelDonation = async (req, res) => {
  try {
    const { id } = req.params;
    
    // Get donation
    const donation = await Donation.findOne({
      where: { 
        id,
        donor_id: req.user.id,
        status: 'Pending'
      },
      include: [
        {
          model: Request,
          as: 'request',
          include: [
            { model: Item, as: 'item' },
            { model: Charity, as: 'charity' }
          ]
        }
      ]
    });
    
    if (!donation) {
      return res.status(404).json({ success: false, message: 'Donation not found or cannot be cancelled' });
    }
    
    // Update donation
    donation.status = 'Cancelled';
    await donation.save();
    
    // Notify charity
    const charityUsers = await User.findAll({
      where: { 
        charity_id: donation.request.charity_id,
        role: 'charity'
      }
    });
    
    if (charityUsers && charityUsers.length > 0) {
      const message = `${req.user.name} has cancelled their donation of ${donation.quantity} ${donation.request.item.unit}(s) of ${donation.request.item.name}.`;
      
      for (const user of charityUsers) {
        await notificationService.notifyRequestUpdate(
          user.id,
          donation.request.id,
          message
        );
      }
    }
    
    return res.status(200).json({ success: true, message: 'Donation cancelled successfully' });
  } catch (error) {
    console.error('Cancel donation error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while cancelling donation' });
  }
};

// View charity details
exports.viewCharity = async (req, res) => {
  try {
    const { id } = req.params;
    
    // Get charity
    const charity = await Charity.findOne({
      where: { id, status: 'approved' }
    });
    
    if (!charity) {
      return res.status(404).render('error', {
        title: 'Error',
        message: 'Charity not found',
        error: {}
      });
    }
    
    // Get charity's active requests
    const activeRequests = await Request.findAll({
      where: { 
        charity_id: id,
        status: 'Pending'
      },
      include: [
        { model: Item, as: 'item' }
      ],
      order: [['created_at', 'DESC']],
      limit: 5
    });
    
    res.render('donor/charity-details', {
      title: `Charity: ${charity.name}`,
      user: req.user,
      charity,
      activeRequests
    });
  } catch (error) {
    console.error('View charity error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading charity details',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// View notifications
exports.viewNotifications = async (req, res) => {
  try {
    // Get user notifications
    const notifications = await notificationService.getUserNotifications(req.user.id);
    
    res.render('donor/notifications', {
      title: 'Notifications',
      user: req.user,
      notifications
    });
  } catch (error) {
    console.error('View notifications error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading notifications',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// Update profile
exports.updateProfile = async (req, res) => {
  try {
    const { name, email } = req.body;
    
    // Get user
    const user = await User.findByPk(req.user.id);
    
    if (!user) {
      return res.status(404).json({ success: false, message: 'User not found' });
    }
    
    // Check if email is already in use
    if (email !== user.email) {
      const existingUser = await User.findOne({ where: { email } });
      
      if (existingUser) {
        return res.status(400).json({ success: false, message: 'Email already in use' });
      }
    }
    
    // Update user
    user.name = name;
    user.email = email;
    await user.save();
    
    return res.status(200).json({ success: true, message: 'Profile updated successfully' });
  } catch (error) {
    console.error('Update profile error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while updating profile' });
  }
};

// Change password
exports.changePassword = async (req, res) => {
  try {
    const { current_password, new_password } = req.body;
    
    // Get user
    const user = await User.findByPk(req.user.id);
    
    if (!user) {
      return res.status(404).json({ success: false, message: 'User not found' });
    }
    
    // Check current password
    const isPasswordValid = await user.validPassword(current_password);
    
    if (!isPasswordValid) {
      return res.status(401).json({ success: false, message: 'Current password is incorrect' });
    }
    
    // Update password
    user.password = new_password; // Will be hashed in model hook
    await user.save();
    
    return res.status(200).json({ success: true, message: 'Password changed successfully' });
  } catch (error) {
    console.error('Change password error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while changing password' });
  }
};
