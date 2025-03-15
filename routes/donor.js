const express = require('express');
const router = express.Router();
const donorController = require('../controllers/donorController');
const { isAuthenticated, isDonor } = require('../middleware/auth');

// Apply authentication and donor role check to all routes
router.use(isAuthenticated, isDonor);

// Dashboard
router.get('/dashboard', donorController.getDashboard);

// Browse donation requests
router.get('/browse', donorController.browseRequests);
router.get('/requests/:id', donorController.viewRequest);
router.post('/requests/:id/donate', donorController.makeDonation);

// My donations
router.get('/my-donations', donorController.viewMyDonations);
router.post('/donations/:id/cancel', donorController.cancelDonation);

// View charity details
router.get('/charities/:id', donorController.viewCharity);

// Notifications
router.get('/notifications', donorController.viewNotifications);

// Profile management
router.post('/profile', donorController.updateProfile);
router.post('/change-password', donorController.changePassword);

module.exports = router;
