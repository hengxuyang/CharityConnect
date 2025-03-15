const { User, Charity, Request, Donation, Item, Category, Inventory } = require('../models');
const { Op } = require('sequelize');
const { v4: uuidv4 } = require('uuid');

// Get categories
exports.getCategories = async (req, res) => {
  try {
    const categories = await Category.findAll({
      order: [['name', 'ASC']]
    });
    
    return res.status(200).json({ success: true, categories });
  } catch (error) {
    console.error('Get categories error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while fetching categories' });
  }
};

// Get items by category
exports.getItemsByCategory = async (req, res) => {
  try {
    const { categoryId } = req.params;
    
    const items = await Item.findAll({
      where: { category_id: categoryId },
      order: [['name', 'ASC']]
    });
    
    return res.status(200).json({ success: true, items });
  } catch (error) {
    console.error('Get items by category error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while fetching items' });
  }
};

// Get all items
exports.getAllItems = async (req, res) => {
  try {
    const items = await Item.findAll({
      include: [
        { model: Category, as: 'category' }
      ],
      order: [['name', 'ASC']]
    });
    
    return res.status(200).json({ success: true, items });
  } catch (error) {
    console.error('Get all items error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while fetching items' });
  }
};

// Create new item
exports.createItem = async (req, res) => {
  try {
    const { name, category_id, unit, expiry_date } = req.body;
    
    // Check if item already exists
    const existingItem = await Item.findOne({
      where: {
        name,
        category_id
      }
    });
    
    if (existingItem) {
      return res.status(400).json({ success: false, message: 'Item already exists' });
    }
    
    // Create item
    const item = await Item.create({
      id: uuidv4(),
      name,
      category_id,
      unit,
      expiry_date: expiry_date || null,
      created_at: new Date()
    });
    
    return res.status(201).json({ success: true, item });
  } catch (error) {
    console.error('Create item error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while creating item' });
  }
};

// Get charity requests
exports.getCharityRequests = async (req, res) => {
  try {
    const { charityId } = req.params;
    
    const requests = await Request.findAll({
      where: { 
        charity_id: charityId,
        status: 'Pending'
      },
      include: [
        { model: Item, as: 'item' }
      ],
      order: [['created_at', 'DESC']]
    });
    
    return res.status(200).json({ success: true, requests });
  } catch (error) {
    console.error('Get charity requests error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while fetching requests' });
  }
};

// Get request details
exports.getRequestDetails = async (req, res) => {
  try {
    const { requestId } = req.params;
    
    const request = await Request.findByPk(requestId, {
      include: [
        { 
          model: Item, 
          as: 'item',
          include: [
            { model: Category, as: 'category' }
          ]
        },
        { model: Charity, as: 'charity' },
        { 
          model: Donation, 
          as: 'donations',
          include: [
            { model: User, as: 'donor' }
          ]
        }
      ]
    });
    
    if (!request) {
      return res.status(404).json({ success: false, message: 'Request not found' });
    }
    
    return res.status(200).json({ success: true, request });
  } catch (error) {
    console.error('Get request details error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while fetching request details' });
  }
};

// Get donation details
exports.getDonationDetails = async (req, res) => {
  try {
    const { donationId } = req.params;
    
    const donation = await Donation.findByPk(donationId, {
      include: [
        { model: User, as: 'donor' },
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
      return res.status(404).json({ success: false, message: 'Donation not found' });
    }
    
    return res.status(200).json({ success: true, donation });
  } catch (error) {
    console.error('Get donation details error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while fetching donation details' });
  }
};

// Get inventory item details
exports.getInventoryItemDetails = async (req, res) => {
  try {
    const { inventoryId } = req.params;
    
    const inventoryItem = await Inventory.findByPk(inventoryId, {
      include: [
        { model: Item, as: 'item' }
      ]
    });
    
    if (!inventoryItem) {
      return res.status(404).json({ success: false, message: 'Inventory item not found' });
    }
    
    return res.status(200).json({ success: true, inventoryItem });
  } catch (error) {
    console.error('Get inventory item details error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while fetching inventory item details' });
  }
};

// Search requests
exports.searchRequests = async (req, res) => {
  try {
    const { query, category, charity, request_type } = req.query;
    
    // Build query conditions
    const whereConditions = { status: 'Pending' };
    const itemConditions = {};
    const charityConditions = {};
    
    if (query) {
      itemConditions.name = { [Op.like]: `%${query}%` };
    }
    
    if (category) {
      itemConditions.category_id = category;
    }
    
    if (charity) {
      whereConditions.charity_id = charity;
    }
    
    if (request_type && ['drop-off', 'pickup'].includes(request_type)) {
      whereConditions.request_type = request_type;
    }
    
    // Get requests
    const requests = await Request.findAll({
      where: whereConditions,
      include: [
        { 
          model: Item, 
          as: 'item',
          where: Object.keys(itemConditions).length > 0 ? itemConditions : undefined,
          include: [
            { model: Category, as: 'category' }
          ]
        },
        { model: Charity, as: 'charity' }
      ],
      order: [['created_at', 'DESC']]
    });
    
    return res.status(200).json({ success: true, requests });
  } catch (error) {
    console.error('Search requests error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while searching requests' });
  }
};

// Get user profile
exports.getUserProfile = async (req, res) => {
  try {
    const user = await User.findByPk(req.user.id, {
      attributes: { exclude: ['password'] }
    });
    
    if (!user) {
      return res.status(404).json({ success: false, message: 'User not found' });
    }
    
    // If user is a charity, get charity details
    let charity = null;
    if (user.role === 'charity' && user.charity_id) {
      charity = await Charity.findByPk(user.charity_id);
    }
    
    return res.status(200).json({ 
      success: true, 
      user: {
        ...user.toJSON(),
        charity: charity ? charity.toJSON() : null
      }
    });
  } catch (error) {
    console.error('Get user profile error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while fetching user profile' });
  }
};

// Get dashboard stats
exports.getDashboardStats = async (req, res) => {
  try {
    let stats = {};
    
    // Different stats based on user role
    switch (req.user.role) {
      case 'admin':
        stats = await getAdminStats();
        break;
      case 'charity':
        stats = await getCharityStats(req.user.charity_id);
        break;
      case 'public_user':
        stats = await getDonorStats(req.user.id);
        break;
    }
    
    return res.status(200).json({ success: true, stats });
  } catch (error) {
    console.error('Get dashboard stats error:', error);
    return res.status(500).json({ success: false, message: 'An error occurred while fetching dashboard stats' });
  }
};

// Helper function to get admin stats
const getAdminStats = async () => {
  const [
    totalUsers,
    totalCharities,
    pendingCharities,
    totalRequests,
    totalDonations
  ] = await Promise.all([
    User.count(),
    Charity.count(),
    Charity.count({ where: { status: 'pending' } }),
    Request.count(),
    Donation.count()
  ]);
  
  return {
    totalUsers,
    totalCharities,
    pendingCharities,
    totalRequests,
    totalDonations
  };
};

// Helper function to get charity stats
const getCharityStats = async (charityId) => {
  const [
    totalRequests,
    pendingRequests,
    fulfilledRequests,
    totalDonations,
    pendingDonations,
    deliveredDonations
  ] = await Promise.all([
    Request.count({ where: { charity_id: charityId } }),
    Request.count({ where: { charity_id: charityId, status: 'Pending' } }),
    Request.count({ where: { charity_id: charityId, status: 'Fulfilled' } }),
    Donation.count({
      include: [
        {
          model: Request,
          as: 'request',
          where: { charity_id: charityId }
        }
      ]
    }),
    Donation.count({
      where: { status: 'Pending' },
      include: [
        {
          model: Request,
          as: 'request',
          where: { charity_id: charityId }
        }
      ]
    }),
    Donation.count({
      where: { status: 'Delivered' },
      include: [
        {
          model: Request,
          as: 'request',
          where: { charity_id: charityId }
        }
      ]
    })
  ]);
  
  return {
    totalRequests,
    pendingRequests,
    fulfilledRequests,
    totalDonations,
    pendingDonations,
    deliveredDonations
  };
};

// Helper function to get donor stats
const getDonorStats = async (userId) => {
  const [
    totalDonations,
    pendingDonations,
    deliveredDonations,
    cancelledDonations,
    availableRequests
  ] = await Promise.all([
    Donation.count({ where: { donor_id: userId } }),
    Donation.count({ where: { donor_id: userId, status: 'Pending' } }),
    Donation.count({ where: { donor_id: userId, status: 'Delivered' } }),
    Donation.count({ where: { donor_id: userId, status: 'Cancelled' } }),
    Request.count({ where: { status: 'Pending' } })
  ]);
  
  return {
    totalDonations,
    pendingDonations,
    deliveredDonations,
    cancelledDonations,
    availableRequests
  };
};
