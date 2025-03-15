const { Charity, User, Request, Donation, Item, Category, Inventory } = require('../models');
const { Op } = require('sequelize');
const { v4: uuidv4 } = require('uuid');
const notificationService = require('../utils/notification');

// Charity dashboard
exports.getDashboard = async (req, res) => {
  try {
    // Get charity info
    const charity = await Charity.findByPk(req.user.charity_id);
    
    if (!charity) {
      return res.status(404).render('error', {
        title: 'Error',
        message: 'Charity not found',
        error: {}
      });
    }
    
    // Get counts for dashboard
    const [
      totalRequests,
      pendingRequests,
      fulfilledRequests,
      totalDonations,
      pendingDonations,
      deliveredDonations
    ] = await Promise.all([
      Request.count({ where: { charity_id: charity.id } }),
      Request.count({ where: { charity_id: charity.id, status: 'Pending' } }),
      Request.count({ where: { charity_id: charity.id, status: 'Fulfilled' } }),
      Donation.count({
        include: [
          {
            model: Request,
            as: 'request',
            where: { charity_id: charity.id }
          }
        ]
      }),
      Donation.count({
        where: { status: 'Pending' },
        include: [
          {
            model: Request,
            as: 'request',
            where: { charity_id: charity.id }
          }
        ]
      }),
      Donation.count({
        where: { status: 'Delivered' },
        include: [
          {
            model: Request,
            as: 'request',
            where: { charity_id: charity.id }
          }
        ]
      })
    ]);
    
    // Get recent requests
    const recentRequests = await Request.findAll({
      where: { charity_id: charity.id },
      include: [
        { model: Item, as: 'item' }
      ],
      order: [['created_at', 'DESC']],
      limit: 5
    });
    
    // Get recent donations
    const recentDonations = await Donation.findAll({
      include: [
        {
          model: Request,
          as: 'request',
          where: { charity_id: charity.id },
          include: [
            { model: Item, as: 'item' }
          ]
        },
        { model: User, as: 'donor' }
      ],
      order: [['created_at', 'DESC']],
      limit: 5
    });
    
    // Get low stock items
    const lowStockItems = await Inventory.findAll({
      where: { 
        charity_id: charity.id,
        quantity: { [Op.lt]: 10 } // Example threshold
      },
      include: [
        { model: Item, as: 'item' }
      ],
      order: [['quantity', 'ASC']],
      limit: 5
    });
    
    res.render('charity/dashboard', {
      title: 'Charity Dashboard',
      user: req.user,
      charity,
      stats: {
        totalRequests,
        pendingRequests,
        fulfilledRequests,
        totalDonations,
        pendingDonations,
        deliveredDonations
      },
      recentRequests,
      recentDonations,
      lowStockItems
    });
  } catch (error) {
    console.error('Charity dashboard error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading the dashboard',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// Charity profile
exports.getProfile = async (req, res) => {
  try {
    // Get charity info
    const charity = await Charity.findByPk(req.user.charity_id);
    
    if (!charity) {
      return res.status(404).render('error', {
        title: 'Error',
        message: 'Charity not found',
        error: {}
      });
    }
    
    // Get charity users
    const charityUsers = await User.findAll({
      where: { 
        charity_id: charity.id,
        role: 'charity'
      }
    });
    
    res.render('charity/profile', {
      title: 'Charity Profile',
      user: req.user,
      charity,
      charityUsers
    });
  } catch (error) {
    console.error('Charity profile error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading the profile',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// Update charity profile
exports.updateProfile = async (req, res) => {
  try {
    const { name, phone, address } = req.body;
    
    // Get charity
    const charity = await Charity.findByPk(req.user.charity_id);
    
    if (!charity) {
      return res.status(404).json({ success: false, message: 'Charity not found' });
    }
    
    // Update charity
    charity.name = name;
    charity.phone = phone;
    charity.address = address;
    await charity.save();
    
    return res.status(200).json({ success: true, message: 'Profile updated successfully' });
  } catch (error) {
    console.error('Update charity profile error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while updating profile' });
  }
};

// List donation requests
exports.listRequests = async (req, res) => {
  try {
    const { status, category } = req.query;
    
    // Build query conditions
    const whereConditions = { charity_id: req.user.charity_id };
    
    if (status && ['Pending', 'Fulfilled', 'Cancelled'].includes(status)) {
      whereConditions.status = status;
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
    
    res.render('charity/requests', {
      title: 'Donation Requests',
      user: req.user,
      requests: filteredRequests,
      categories,
      filters: { status, category }
    });
  } catch (error) {
    console.error('List requests error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading requests',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// Create request form
exports.createRequestForm = async (req, res) => {
  try {
    // Get charity
    const charity = await Charity.findByPk(req.user.charity_id);
    
    if (!charity) {
      return res.status(404).render('error', {
        title: 'Error',
        message: 'Charity not found',
        error: {}
      });
    }
    
    // Get categories and items
    const categories = await Category.findAll({
      order: [['name', 'ASC']],
      include: [
        { model: Item, as: 'items' }
      ]
    });
    
    res.render('charity/create-request', {
      title: 'Create Donation Request',
      user: req.user,
      charity,
      categories
    });
  } catch (error) {
    console.error('Create request form error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading the form',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// Create request
exports.createRequest = async (req, res) => {
  try {
    const { 
      item_id, 
      quantity, 
      request_type, 
      drop_off_address, 
      available_times 
    } = req.body;
    
    // Create request
    const request = await Request.create({
      id: uuidv4(),
      charity_id: req.user.charity_id,
      item_id,
      quantity,
      request_type,
      drop_off_address,
      available_times: new Date(available_times),
      status: 'Pending',
      created_at: new Date()
    });
    
    // Handle image upload if provided
    if (req.file) {
      // In a real app, you'd store the file path in the request record
      console.log('File uploaded:', req.file.filename);
    }
    
    return res.status(201).redirect('/charity/requests');
  } catch (error) {
    console.error('Create request error:', error);
    return res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while creating the request',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// View request
exports.viewRequest = async (req, res) => {
  try {
    const { id } = req.params;
    
    // Get request
    const request = await Request.findOne({
      where: { 
        id,
        charity_id: req.user.charity_id
      },
      include: [
        { 
          model: Item, 
          as: 'item',
          include: [
            { model: Category, as: 'category' }
          ]
        }
      ]
    });
    
    if (!request) {
      return res.status(404).render('error', {
        title: 'Error',
        message: 'Request not found',
        error: {}
      });
    }
    
    // Get donations for this request
    const donations = await Donation.findAll({
      where: { request_id: id },
      include: [
        { model: User, as: 'donor' }
      ],
      order: [['created_at', 'DESC']]
    });
    
    res.render('charity/request-details', {
      title: 'Request Details',
      user: req.user,
      request,
      donations
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

// Update request
exports.updateRequest = async (req, res) => {
  try {
    const { id } = req.params;
    const { 
      quantity, 
      request_type, 
      drop_off_address, 
      available_times,
      status
    } = req.body;
    
    // Get request
    const request = await Request.findOne({
      where: { 
        id,
        charity_id: req.user.charity_id
      }
    });
    
    if (!request) {
      return res.status(404).json({ success: false, message: 'Request not found' });
    }
    
    // Update request
    request.quantity = quantity;
    request.request_type = request_type;
    request.drop_off_address = drop_off_address;
    request.available_times = new Date(available_times);
    if (status) {
      request.status = status;
    }
    await request.save();
    
    return res.status(200).json({ success: true, message: 'Request updated successfully' });
  } catch (error) {
    console.error('Update request error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while updating request' });
  }
};

// Cancel request
exports.cancelRequest = async (req, res) => {
  try {
    const { id } = req.params;
    
    // Get request
    const request = await Request.findOne({
      where: { 
        id,
        charity_id: req.user.charity_id
      },
      include: [
        { model: Donation, as: 'donations' }
      ]
    });
    
    if (!request) {
      return res.status(404).json({ success: false, message: 'Request not found' });
    }
    
    // Check if there are any pending donations
    const pendingDonations = request.donations.filter(donation => donation.status === 'Pending');
    
    if (pendingDonations.length > 0) {
      return res.status(400).json({ 
        success: false, 
        message: 'Cannot cancel request with pending donations' 
      });
    }
    
    // Update request
    request.status = 'Cancelled';
    await request.save();
    
    return res.status(200).json({ success: true, message: 'Request cancelled successfully' });
  } catch (error) {
    console.error('Cancel request error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while cancelling request' });
  }
};

// Manage inventory
exports.manageInventory = async (req, res) => {
  try {
    const { category } = req.query;
    
    // Get inventory items
    const inventory = await Inventory.findAll({
      where: { charity_id: req.user.charity_id },
      include: [
        { 
          model: Item, 
          as: 'item',
          include: [
            { model: Category, as: 'category' }
          ]
        }
      ],
      order: [['last_updated', 'DESC']]
    });
    
    // Filter by category if specified
    const filteredInventory = category 
      ? inventory.filter(inv => inv.item.category_id === parseInt(category))
      : inventory;
    
    // Get categories for filter
    const categories = await Category.findAll({
      order: [['name', 'ASC']]
    });
    
    // Get all items for adding new inventory
    const items = await Item.findAll({
      include: [
        { model: Category, as: 'category' }
      ],
      order: [['name', 'ASC']]
    });
    
    res.render('charity/inventory', {
      title: 'Manage Inventory',
      user: req.user,
      inventory: filteredInventory,
      categories,
      items,
      filters: { category }
    });
  } catch (error) {
    console.error('Manage inventory error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading inventory',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// Update inventory
exports.updateInventory = async (req, res) => {
  try {
    const { item_id, quantity } = req.body;
    
    // Check if inventory item exists
    let inventoryItem = await Inventory.findOne({
      where: { 
        charity_id: req.user.charity_id,
        item_id
      }
    });
    
    if (inventoryItem) {
      // Update existing inventory
      inventoryItem.quantity = quantity;
      inventoryItem.last_updated = new Date();
      await inventoryItem.save();
    } else {
      // Create new inventory item
      inventoryItem = await Inventory.create({
        id: uuidv4(),
        charity_id: req.user.charity_id,
        item_id,
        quantity,
        last_updated: new Date()
      });
    }
    
    // Check if inventory is low and send notification if needed
    if (quantity < 10) { // Example threshold
      const item = await Item.findByPk(item_id);
      await notificationService.notifyInventoryAlert(
        req.user.charity_id,
        `Low inventory alert: ${item.name} is running low (${quantity} ${item.unit}s remaining)`
      );
    }
    
    return res.status(200).json({ success: true, message: 'Inventory updated successfully' });
  } catch (error) {
    console.error('Update inventory error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while updating inventory' });
  }
};

// View donations
exports.viewDonations = async (req, res) => {
  try {
    const { status } = req.query;
    
    // Get donations for charity's requests
    const donations = await Donation.findAll({
      include: [
        {
          model: Request,
          as: 'request',
          where: { charity_id: req.user.charity_id },
          include: [
            { model: Item, as: 'item' }
          ]
        },
        { model: User, as: 'donor' }
      ],
      order: [['created_at', 'DESC']]
    });
    
    // Filter by status if specified
    const filteredDonations = status 
      ? donations.filter(donation => donation.status === status)
      : donations;
    
    res.render('charity/donations', {
      title: 'View Donations',
      user: req.user,
      donations: filteredDonations,
      filters: { status }
    });
  } catch (error) {
    console.error('View donations error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading donations',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// Update donation status
exports.updateDonationStatus = async (req, res) => {
  try {
    const { id } = req.params;
    const { status } = req.body;
    
    // Get donation
    const donation = await Donation.findOne({
      where: { id },
      include: [
        {
          model: Request,
          as: 'request',
          where: { charity_id: req.user.charity_id }
        },
        { model: User, as: 'donor' }
      ]
    });
    
    if (!donation) {
      return res.status(404).json({ success: false, message: 'Donation not found' });
    }
    
    // Update donation
    donation.status = status;
    await donation.save();
    
    // If donation is delivered, update inventory
    if (status === 'Delivered') {
      // Get request and item
      const request = await Request.findByPk(donation.request_id, {
        include: [
          { model: Item, as: 'item' }
        ]
      });
      
      // Update inventory
      let inventoryItem = await Inventory.findOne({
        where: { 
          charity_id: req.user.charity_id,
          item_id: request.item_id
        }
      });
      
      if (inventoryItem) {
        // Update existing inventory
        inventoryItem.quantity += donation.quantity;
        inventoryItem.last_updated = new Date();
        await inventoryItem.save();
      } else {
        // Create new inventory item
        inventoryItem = await Inventory.create({
          id: uuidv4(),
          charity_id: req.user.charity_id,
          item_id: request.item_id,
          quantity: donation.quantity,
          last_updated: new Date()
        });
      }
      
      // Check if request is fulfilled
      const totalDonated = await Donation.sum('quantity', {
        where: { 
          request_id: request.id,
          status: 'Delivered'
        }
      });
      
      if (totalDonated >= request.quantity) {
        request.status = 'Fulfilled';
        await request.save();
      }
      
      // Notify donor
      await notificationService.notifyDonationUpdate(
        donation.donor_id,
        donation.id,
        `Your donation of ${donation.quantity} ${request.item.unit}(s) of ${request.item.name} has been received by ${req.user.name}. Thank you for your generosity!`
      );
    }
    
    return res.status(200).json({ success: true, message: 'Donation status updated successfully' });
  } catch (error) {
    console.error('Update donation status error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while updating donation status' });
  }
};

// Send thank you note
exports.sendThankYou = async (req, res) => {
  try {
    const { id } = req.params;
    const { message } = req.body;
    
    // Get donation
    const donation = await Donation.findOne({
      where: { id },
      include: [
        {
          model: Request,
          as: 'request',
          where: { charity_id: req.user.charity_id }
        }
      ]
    });
    
    if (!donation) {
      return res.status(404).json({ success: false, message: 'Donation not found' });
    }
    
    // Send thank you note
    await notificationService.sendThankYouNote(
      donation.donor_id,
      req.user.charity_id,
      donation.id,
      message
    );
    
    return res.status(200).json({ success: true, message: 'Thank you note sent successfully' });
  } catch (error) {
    console.error('Send thank you note error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while sending thank you note' });
  }
};
