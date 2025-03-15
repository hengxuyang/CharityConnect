const { User, Charity } = require('../models');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcrypt');
const { v4: uuidv4 } = require('uuid');
const { validationResult } = require('express-validator');
const emailService = require('../utils/email');

// Generate JWT token
const generateToken = (user) => {
    const token = jwt.sign(
      { id: user.id, email: user.email, role: user.role },
      process.env.JWT_SECRET,
      { expiresIn: '7d' }
    );
    console.log('Generated token:', token);
    return token;
  };
  

// Register a new user
exports.register = async (req, res) => {
  try {
    // Check for validation errors
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).render('auth/register', {
        title: 'Register',
        errors: errors.array(),
        formData: req.body
      });
    }

    const { name, email, password, role } = req.body;

    // Check if user already exists
    const existingUser = await User.findOne({ where: { email } });
    if (existingUser) {
      return res.status(400).render('auth/register', {
        title: 'Register',
        error: 'Email already in use',
        formData: req.body
      });
    }

    // If registering as charity, create charity record first
    let charityId = null;
    if (role === 'charity') {
      const charity = await Charity.create({
        id: uuidv4(),
        name: req.body.charity_name || name,
        email: email,
        phone: req.body.phone || '',
        address: req.body.address || '',
        status: 'pending',
        created_at: new Date()
      });
      charityId = charity.id;
    }

    // Create user
    const user = await User.create({
      id: uuidv4(),
      name,
      email,
      password, // Hashed in model hook
      role,
      charity_id: charityId,
      created_at: new Date()
    });

    // Generate verification token
    const verificationToken = jwt.sign(
      { id: user.id, email: user.email },
      process.env.JWT_SECRET,
      { expiresIn: '24h' }
    );

    // Send verification email
    await emailService.sendVerificationEmail(email, verificationToken);

    // Redirect to login page with success message
    return res.status(201).render('auth/login', {
      title: 'Login',
      success: 'Registration successful! Please check your email to verify your account.'
    });
  } catch (error) {
    console.error('Registration error:', error);
    return res.status(500).render('auth/register', {
      title: 'Register',
      error: 'An error occurred during registration. Please try again.',
      formData: req.body
    });
  }
};

// Verify email
exports.verifyEmail = async (req, res) => {
  try {
    const { token } = req.query;
    
    if (!token) {
      return res.status(400).render('auth/verify-email', {
        title: 'Verify Email',
        error: 'Invalid verification link'
      });
    }

    // Verify token
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    
    // Find user
    const user = await User.findByPk(decoded.id);
    
    if (!user) {
      return res.status(400).render('auth/verify-email', {
        title: 'Verify Email',
        error: 'User not found'
      });
    }

    // Update user (in a real app, you'd have a verified field)
    // For now, we'll just show a success message

    return res.status(200).render('auth/verify-email', {
      title: 'Verify Email',
      success: 'Email verified successfully! You can now log in.'
    });
  } catch (error) {
    console.error('Email verification error:', error);
    return res.status(500).render('auth/verify-email', {
      title: 'Verify Email',
      error: 'An error occurred during email verification. Please try again.'
    });
  }
};

// Login
exports.login = async (req, res) => {

  try {
    // Check for validation errors
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).render('auth/login', {
        title: 'Login',
        errors: errors.array(),
        formData: req.body
      });
    }

    const { email, password } = req.body;
    // Find user
    const user = await User.findOne({ where: { email } });
    
    if (!user) {
      return res.status(401).render('auth/login', {
        title: 'Login',
        error: 'Invalid email or password',
        formData: { email }
      });
    }
    hashpass = await bcrypt.hash(password, 10);
    console.log('hash password', hashpass);

    // Check password
    const isPasswordValid = await user.validPassword(password);
    
    if (!isPasswordValid) {
      return res.status(401).render('auth/login', {
        title: 'Login',
        error: 'Invalid email or password',
        formData: { email }
      });
    }

    // Generate token
    const token = generateToken(user);

    // Set cookie
    res.cookie('token', token, {
      httpOnly: true,
      secure: process.env.NODE_ENV === 'production',
      maxAge: 7 * 24 * 60 * 60 * 1000 // 7 days
    });

    // Redirect based on role
    switch (user.role) {
      case 'admin':
        return res.redirect('/admin/dashboard');
      case 'charity':
        return res.redirect('/charity/dashboard');
      case 'public_user':
        return res.redirect('/donor/dashboard');
      default:
        return res.redirect('/');
    }
  } catch (error) {
    console.error('Login error:', error);
    return res.status(500).render('auth/login', {
      title: 'Login',
      error: 'An error occurred during login. Please try again.',
      formData: req.body
    });
  }
};

// Forgot password
exports.forgotPassword = async (req, res) => {
  try {
    // Check for validation errors
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).render('auth/forgot-password', {
        title: 'Forgot Password',
        errors: errors.array(),
        formData: req.body
      });
    }

    const { email } = req.body;

    // Find user
    const user = await User.findOne({ where: { email } });
    
    if (!user) {
      // Don't reveal that the user doesn't exist
      return res.status(200).render('auth/forgot-password', {
        title: 'Forgot Password',
        success: 'If your email is registered, you will receive a password reset link shortly.'
      });
    }

    // Generate reset token
    const resetToken = jwt.sign(
      { id: user.id, email: user.email },
      process.env.JWT_SECRET,
      { expiresIn: '1h' }
    );

    // Send password reset email
    await emailService.sendPasswordResetEmail(email, resetToken);

    return res.status(200).render('auth/forgot-password', {
      title: 'Forgot Password',
      success: 'If your email is registered, you will receive a password reset link shortly.'
    });
  } catch (error) {
    console.error('Forgot password error:', error);
    return res.status(500).render('auth/forgot-password', {
      title: 'Forgot Password',
      error: 'An error occurred. Please try again.',
      formData: req.body
    });
  }
};

// Reset password form
exports.resetPasswordForm = async (req, res) => {
  try {
    const { token } = req.query;
    
    if (!token) {
      return res.status(400).render('auth/reset-password', {
        title: 'Reset Password',
        error: 'Invalid reset link'
      });
    }

    // Verify token
    try {
      jwt.verify(token, process.env.JWT_SECRET);
    } catch (error) {
      return res.status(400).render('auth/reset-password', {
        title: 'Reset Password',
        error: 'Reset link is invalid or has expired'
      });
    }

    return res.status(200).render('auth/reset-password', {
      title: 'Reset Password',
      token
    });
  } catch (error) {
    console.error('Reset password form error:', error);
    return res.status(500).render('auth/reset-password', {
      title: 'Reset Password',
      error: 'An error occurred. Please try again.'
    });
  }
};

// Reset password
exports.resetPassword = async (req, res) => {
  try {
    // Check for validation errors
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).render('auth/reset-password', {
        title: 'Reset Password',
        errors: errors.array(),
        token: req.body.token
      });
    }

    const { token, password } = req.body;
    
    if (!token) {
      return res.status(400).render('auth/reset-password', {
        title: 'Reset Password',
        error: 'Invalid reset link'
      });
    }

    // Verify token
    let decoded;
    try {
      decoded = jwt.verify(token, process.env.JWT_SECRET);
    } catch (error) {
      return res.status(400).render('auth/reset-password', {
        title: 'Reset Password',
        error: 'Reset link is invalid or has expired'
      });
    }

    // Find user
    const user = await User.findByPk(decoded.id);
    
    if (!user) {
      return res.status(400).render('auth/reset-password', {
        title: 'Reset Password',
        error: 'User not found'
      });
    }

    // Update user with plain password
    // The model's beforeUpdate hook will handle the hashing
    user.password = password;
    await user.save();

    return res.status(200).render('auth/login', {
      title: 'Login',
      success: 'Password reset successful! You can now log in with your new password.'
    });
  } catch (error) {
    console.error('Reset password error:', error);
    return res.status(500).render('auth/reset-password', {
      title: 'Reset Password',
      error: 'An error occurred. Please try again.',
      token: req.body.token
    });
  }
};

// Logout
exports.logout = (req, res) => {
  res.clearCookie('token');
  return res.redirect('/');
};

// Get register page
exports.getRegisterPage = (req, res) => {
  res.render('auth/register', {
    title: 'Register',
    formData: {}
  });
};

// Get login page
exports.getLoginPage = (req, res) => {
  res.render('auth/login', {
    title: 'Login',
    formData: {}
  });
};

// Get forgot password page
exports.getForgotPasswordPage = (req, res) => {
  res.render('auth/forgot-password', {
    title: 'Forgot Password',
    formData: {}
  });
};
