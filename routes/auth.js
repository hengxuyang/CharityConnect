const express = require('express');
const router = express.Router();
const { check } = require('express-validator');
const authController = require('../controllers/authController');
const { isAuthenticated } = require('../middleware/auth');

// Validation rules
const registerValidation = [
  check('name', 'Name is required').not().isEmpty(),
  check('email', 'Please include a valid email').isEmail(),
  check('password', 'Password must be at least 6 characters').isLength({ min: 6 }),
  check('role', 'Role is required').isIn(['admin', 'charity', 'public_user'])
];

const loginValidation = [
  check('email', 'Please include a valid email').isEmail(),
  check('password', 'Password is required').exists()
];

const forgotPasswordValidation = [
  check('email', 'Please include a valid email').isEmail()
];

const resetPasswordValidation = [
  check('password', 'Password must be at least 6 characters').isLength({ min: 6 }),
  check('token', 'Token is required').exists()
];

// Register routes
router.get('/register', authController.getRegisterPage);
router.post('/register', registerValidation, authController.register);

// Login routes
router.get('/login', authController.getLoginPage);
router.post('/login', loginValidation, authController.login);

// Email verification
router.get('/verify-email', authController.verifyEmail);

// Forgot password routes
router.get('/forgot-password', authController.getForgotPasswordPage);
router.post('/forgot-password', forgotPasswordValidation, authController.forgotPassword);

// Reset password routes
router.get('/reset-password', authController.resetPasswordForm);
router.post('/reset-password', resetPasswordValidation, authController.resetPassword);

// Logout route
router.get('/logout', isAuthenticated, authController.logout);

module.exports = router;
