const jwt = require('jsonwebtoken');
const { User } = require('../models');

// Middleware to check if user is authenticated
exports.isAuthenticated = async (req, res, next) => {
  try {
    // Check if token exists in cookies or authorization header
    const token = req.cookies.token || 
                 (req.headers.authorization && req.headers.authorization.split(' ')[1]);
    
    if (!token) {
      return res.status(401).render('auth/login', { 
        title: 'Login',
        error: 'Authentication required. Please log in.' 
      });
    }

    // Verify token
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    
    // Find user by id
    const user = await User.findByPk(decoded.id);
    
    if (!user) {
      return res.status(401).render('auth/login', { 
        title: 'Login',
        error: 'User not found. Please log in again.' 
      });
    }

    // Add user to request object
    req.user = user;
    next();
  } catch (error) {
    console.error('Authentication error:', error);
    return res.status(401).render('auth/login', { 
      title: 'Login',
      error: 'Authentication failed. Please log in again.' 
    });
  }
};

// Middleware to check if user has required role
exports.hasRole = (roles) => {
  return (req, res, next) => {
    if (!req.user) {
      return res.status(401).render('auth/login', { 
        title: 'Login',
        error: 'Authentication required. Please log in.' 
      });
    }

    if (!roles.includes(req.user.role)) {
      return res.status(403).render('error', { 
        title: 'Access Denied',
        message: 'You do not have permission to access this resource.',
        error: {}
      });
    }

    next();
  };
};

// Middleware to check if user is a charity
exports.isCharity = (req, res, next) => {
  if (!req.user || req.user.role !== 'charity') {
    return res.status(403).render('error', { 
      title: 'Access Denied',
      message: 'Only charity organizations can access this resource.',
      error: {}
    });
  }
  next();
};

// Middleware to check if user is an admin
exports.isAdmin = (req, res, next) => {
  if (!req.user || req.user.role !== 'admin') {
    return res.status(403).render('error', { 
      title: 'Access Denied',
      message: 'Only administrators can access this resource.',
      error: {}
    });
  }
  next();
};

// Middleware to check if user is a donor
exports.isDonor = (req, res, next) => {
  if (!req.user || req.user.role !== 'public_user') {
    return res.status(403).render('error', { 
      title: 'Access Denied',
      message: 'Only donors can access this resource.',
      error: {}
    });
  }
  next();
};
