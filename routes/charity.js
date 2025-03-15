const express = require('express');
const router = express.Router();
const charityController = require('../controllers/charityController');
const { isAuthenticated, isCharity } = require('../middleware/auth');
const { requestImages } = require('../middleware/upload');

// Apply authentication and charity role check to all routes
router.use(isAuthenticated, isCharity);

// Dashboard
router.get('/dashboard', charityController.getDashboard);

// Profile management
router.get('/profile', charityController.getProfile);
router.post('/profile', charityController.updateProfile);

// Donation requests management
router.get('/requests', charityController.listRequests);
router.get('/requests/create', charityController.createRequestForm);
router.post('/requests/create', requestImages.single('image'), charityController.createRequest);
router.get('/requests/:id', charityController.viewRequest);
router.put('/requests/:id', charityController.updateRequest);
router.post('/requests/:id/cancel', charityController.cancelRequest);

// Inventory management
router.get('/inventory', charityController.manageInventory);
router.post('/inventory', charityController.updateInventory);

// Donations management
router.get('/donations', charityController.viewDonations);
router.put('/donations/:id/status', charityController.updateDonationStatus);
router.post('/donations/:id/thank-you', charityController.sendThankYou);

module.exports = router;
