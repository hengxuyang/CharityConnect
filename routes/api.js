const express = require('express');
const router = express.Router();
const apiController = require('../controllers/apiController');
const { isAuthenticated } = require('../middleware/auth');

// Public API routes (no authentication required)
router.get('/categories', apiController.getCategories);
router.get('/items/category/:categoryId', apiController.getItemsByCategory);
router.get('/items', apiController.getAllItems);

// Protected API routes (authentication required)
router.use(isAuthenticated);

// User profile
router.get('/profile', apiController.getUserProfile);

// Dashboard stats
router.get('/dashboard/stats', apiController.getDashboardStats);

// Items
router.post('/items', apiController.createItem);

// Requests
router.get('/requests/search', apiController.searchRequests);
router.get('/requests/:requestId', apiController.getRequestDetails);
router.get('/charities/:charityId/requests', apiController.getCharityRequests);

// Donations
router.get('/donations/:donationId', apiController.getDonationDetails);

// Inventory
router.get('/inventory/:inventoryId', apiController.getInventoryItemDetails);

module.exports = router;
