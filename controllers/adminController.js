const { User, Charity, Request, Donation, Item, Category } = require('../models');
const { Op } = require('sequelize');
const emailService = require('../utils/email');
const notificationService = require('../utils/notification');

// Admin dashboard
exports.getDashboard = async (req, res) => {
  try {
    // Get counts for dashboard
    const [
      pendingCharities,
      totalCharities,
      totalUsers,
      totalRequests,
      totalDonations
    ] = await Promise.all([
      Charity.count({ where: { status: 'pending' } }),
      Charity.count(),
      User.count(),
      Request.count(),
      Donation.count()
    ]);

    // Get recent charity applications
    const recentCharities = await Charity.findAll({
      where: { status: 'pending' },
      order: [['created_at', 'DESC']],
      limit: 5
    });

    // Get recent donation requests
    const recentRequests = await Request.findAll({
      include: [
        { model: Charity, as: 'charity' },
        { model: Item, as: 'item' }
      ],
      order: [['created_at', 'DESC']],
      limit: 5
    });

    res.render('admin/dashboard', {
      title: 'Admin Dashboard',
      user: req.user,
      stats: {
        pendingCharities,
        totalCharities,
        totalUsers,
        totalRequests,
        totalDonations
      },
      recentCharities,
      recentRequests
    });
  } catch (error) {
    console.error('Admin dashboard error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading the dashboard',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// List all charities
exports.listCharities = async (req, res) => {
  try {
    const { status, search } = req.query;
    
    // Build query conditions
    const whereConditions = {};
    
    if (status && ['pending', 'approved', 'rejected'].includes(status)) {
      whereConditions.status = status;
    }
    
    if (search) {
      whereConditions[Op.or] = [
        { name: { [Op.like]: `%${search}%` } },
        { email: { [Op.like]: `%${search}%` } }
      ];
    }
    
    // Get charities
    const charities = await Charity.findAll({
      where: whereConditions,
      order: [['created_at', 'DESC']]
    });
    
    res.render('admin/charities', {
      title: 'Manage Charities',
      user: req.user,
      charities,
      filters: { status, search }
    });
  } catch (error) {
    console.error('List charities error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading charities',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// View charity details
exports.viewCharity = async (req, res) => {
  try {
    const { id } = req.params;
    
    // Get charity with associated users
    const charity = await Charity.findByPk(id, {
      include: [
        { model: User, as: 'users' }
      ]
    });
    
    if (!charity) {
      return res.status(404).render('error', {
        title: 'Error',
        message: 'Charity not found',
        error: {}
      });
    }
    
    // Get charity's requests
    const requests = await Request.findAll({
      where: { charity_id: id },
      include: [
        { model: Item, as: 'item' }
      ],
      order: [['created_at', 'DESC']]
    });
    
    res.render('admin/charity-details', {
      title: `Charity: ${charity.name}`,
      user: req.user,
      charity,
      requests
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

// Approve charity
exports.approveCharity = async (req, res) => {
  try {
    const { id } = req.params;
    
    // Find charity
    const charity = await Charity.findByPk(id, {
      include: [
        { model: User, as: 'users', where: { role: 'charity' } }
      ]
    });
    
    if (!charity) {
      return res.status(404).json({ success: false, message: 'Charity not found' });
    }
    
    // Update charity status
    charity.status = 'approved';
    await charity.save();
    
    // Notify charity users
    if (charity.users && charity.users.length > 0) {
      const message = `Your charity organization "${charity.name}" has been approved. You can now create donation requests and manage your inventory.`;
      
      for (const user of charity.users) {
        await emailService.sendNotificationEmail(
          user.email,
          'Charity Application Approved',
          message
        );
      }
    }
    
    return res.status(200).json({ success: true, message: 'Charity approved successfully' });
  } catch (error) {
    console.error('Approve charity error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while approving charity' });
  }
};

// Reject charity
exports.rejectCharity = async (req, res) => {
  try {
    const { id } = req.params;
    const { reason } = req.body;
    
    // Find charity
    const charity = await Charity.findByPk(id, {
      include: [
        { model: User, as: 'users', where: { role: 'charity' } }
      ]
    });
    
    if (!charity) {
      return res.status(404).json({ success: false, message: 'Charity not found' });
    }
    
    // Update charity status
    charity.status = 'rejected';
    await charity.save();
    
    // Notify charity users
    if (charity.users && charity.users.length > 0) {
      const message = `Your charity organization "${charity.name}" application has been rejected.${reason ? ` Reason: ${reason}` : ''}`;
      
      for (const user of charity.users) {
        await emailService.sendNotificationEmail(
          user.email,
          'Charity Application Rejected',
          message
        );
      }
    }
    
    return res.status(200).json({ success: true, message: 'Charity rejected successfully' });
  } catch (error) {
    console.error('Reject charity error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while rejecting charity' });
  }
};

// List all users
exports.listUsers = async (req, res) => {
  try {
    const { role, search } = req.query;
    
    // Build query conditions
    const whereConditions = {};
    
    if (role && ['admin', 'charity', 'public_user'].includes(role)) {
      whereConditions.role = role;
    }
    
    if (search) {
      whereConditions[Op.or] = [
        { name: { [Op.like]: `%${search}%` } },
        { email: { [Op.like]: `%${search}%` } }
      ];
    }
    
    // Get users with their associated charity (if applicable)
    const users = await User.findAll({
      where: whereConditions,
      include: [
        { model: Charity, as: 'charity', required: false }
      ],
      order: [['created_at', 'DESC']]
    });
    
    res.render('admin/users', {
      title: 'Manage Users',
      user: req.user,
      users,
      filters: { role, search }
    });
  } catch (error) {
    console.error('List users error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading users',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// Manage categories
exports.manageCategories = async (req, res) => {
  try {
    // Get all categories
    const categories = await Category.findAll({
      order: [['name', 'ASC']]
    });
    
    res.render('admin/categories', {
      title: 'Manage Categories',
      user: req.user,
      categories
    });
  } catch (error) {
    console.error('Manage categories error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading categories',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};

// Add category
exports.addCategory = async (req, res) => {
  try {
    const { name } = req.body;
    
    // Check if category already exists
    const existingCategory = await Category.findOne({ where: { name } });
    
    if (existingCategory) {
      return res.status(400).json({ success: false, message: 'Category already exists' });
    }
    
    // Create category
    await Category.create({ name });
    
    return res.status(201).json({ success: true, message: 'Category added successfully' });
  } catch (error) {
    console.error('Add category error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while adding category' });
  }
};

// Delete category
exports.deleteCategory = async (req, res) => {
  try {
    const { id } = req.params;
    
    // Check if category is in use
    const itemsUsingCategory = await Item.count({ where: { category_id: id } });
    
    if (itemsUsingCategory > 0) {
      return res.status(400).json({ 
        success: false, 
        message: `Cannot delete category. It is being used by ${itemsUsingCategory} items.` 
      });
    }
    
    // Delete category
    await Category.destroy({ where: { id } });
    
    return res.status(200).json({ success: true, message: 'Category deleted successfully' });
  } catch (error) {
    console.error('Delete category error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while deleting category' });
  }
};

// View system statistics
exports.getStatistics = async (req, res) => {
  try {
    // Get overall statistics
    const [
      totalUsers,
      totalCharities,
      totalRequests,
      totalDonations,
      pendingRequests,
      fulfilledRequests,
      pendingDonations,
      deliveredDonations
    ] = await Promise.all([
      User.count(),
      Charity.count({ where: { status: 'approved' } }),
      Request.count(),
      Donation.count(),
      Request.count({ where: { status: 'Pending' } }),
      Request.count({ where: { status: 'Fulfilled' } }),
      Donation.count({ where: { status: 'Pending' } }),
      Donation.count({ where: { status: 'Delivered' } })
    ]);
    
    // Get top categories
    const categories = await Category.findAll({
      include: [
        { 
          model: Item, 
          as: 'items',
          include: [
            { model: Request, as: 'requests' }
          ]
        }
      ]
    });
    
    // Process category data for chart
    const categoryData = categories.map(category => {
      const requestCount = category.items.reduce((total, item) => {
        return total + item.requests.length;
      }, 0);
      
      return {
        name: category.name,
        requestCount
      };
    }).sort((a, b) => b.requestCount - a.requestCount);
    
    res.render('admin/statistics', {
      title: 'System Statistics',
      user: req.user,
      stats: {
        totalUsers,
        totalCharities,
        totalRequests,
        totalDonations,
        pendingRequests,
        fulfilledRequests,
        pendingDonations,
        deliveredDonations
      },
      categoryData
    });
  } catch (error) {
    console.error('Statistics error:', error);
    res.status(500).render('error', {
      title: 'Error',
      message: 'An error occurred while loading statistics',
      error: process.env.NODE_ENV === 'development' ? error : {}
    });
  }
};
