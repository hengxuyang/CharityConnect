const express = require('express');
const router = express.Router();
const adminController = require('../controllers/adminController');
const { isAuthenticated, isAdmin } = require('../middleware/auth');

// Apply authentication and admin role check to all routes
router.use(isAuthenticated, isAdmin);

// Dashboard
router.get('/dashboard', adminController.getDashboard);

// Charity management
router.get('/charities', adminController.listCharities);
router.get('/charities/:id', adminController.viewCharity);
router.post('/charities/:id/approve', adminController.approveCharity);
router.post('/charities/:id/reject', adminController.rejectCharity);

// User management
router.get('/users', adminController.listUsers);

// Category management
router.get('/categories', adminController.manageCategories);
router.post('/categories', adminController.addCategory);
router.delete('/categories/:id', adminController.deleteCategory);

// Statistics
router.get('/statistics', adminController.getStatistics);

module.exports = router;
