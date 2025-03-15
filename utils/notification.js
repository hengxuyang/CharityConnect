const { Notification, User, Charity } = require('../models');
const emailService = require('./email');

// Create a notification in the database
const createNotification = async (data) => {
  try {
    const notification = await Notification.create(data);
    return notification;
  } catch (error) {
    console.error('Error creating notification:', error);
    return null;
  }
};

// Send email notification
const sendEmailNotification = async (userId, subject, message) => {
  try {
    const user = await User.findByPk(userId);
    if (!user) {
      throw new Error('User not found');
    }
    
    await emailService.sendNotificationEmail(user.email, subject, message);
    return true;
  } catch (error) {
    console.error('Error sending email notification:', error);
    return false;
  }
};

// Notify user about request update
exports.notifyRequestUpdate = async (userId, requestId, message) => {
  try {
    // Create database notification
    await createNotification({
      user_id: userId,
      request_id: requestId,
      type: 'request_update',
      message: message
    });
    
    // Send email notification
    await sendEmailNotification(userId, 'Donation Request Update', message);
    
    return true;
  } catch (error) {
    console.error('Error notifying request update:', error);
    return false;
  }
};

// Notify user about donation update
exports.notifyDonationUpdate = async (userId, donationId, message) => {
  try {
    // Create database notification
    await createNotification({
      user_id: userId,
      donation_id: donationId,
      type: 'donation_update',
      message: message
    });
    
    // Send email notification
    await sendEmailNotification(userId, 'Donation Update', message);
    
    return true;
  } catch (error) {
    console.error('Error notifying donation update:', error);
    return false;
  }
};

// Notify charity about inventory alert
exports.notifyInventoryAlert = async (charityId, message) => {
  try {
    // Find charity users
    const charity = await Charity.findByPk(charityId, {
      include: [{
        model: User,
        as: 'users',
        where: { role: 'charity' }
      }]
    });
    
    if (!charity || !charity.users || charity.users.length === 0) {
      throw new Error('No charity users found');
    }
    
    // Create notifications for each charity user
    for (const user of charity.users) {
      // Create database notification
      await createNotification({
        user_id: user.id,
        charity_id: charityId,
        type: 'inventory_alert',
        message: message
      });
      
      // Send email notification
      await sendEmailNotification(user.id, 'Inventory Alert', message);
    }
    
    return true;
  } catch (error) {
    console.error('Error notifying inventory alert:', error);
    return false;
  }
};

// Send thank you note to donor
exports.sendThankYouNote = async (donorId, charityId, donationId, message) => {
  try {
    const donor = await User.findByPk(donorId);
    const charity = await Charity.findByPk(charityId);
    
    if (!donor || !charity) {
      throw new Error('Donor or charity not found');
    }
    
    // Create database notification
    await createNotification({
      user_id: donorId,
      charity_id: charityId,
      donation_id: donationId,
      type: 'thank_you_note',
      message: message
    });
    
    // Send thank you email
    await emailService.sendThankYouEmail(
      donor.email,
      charity.name,
      message
    );
    
    return true;
  } catch (error) {
    console.error('Error sending thank you note:', error);
    return false;
  }
};

// Get user notifications
exports.getUserNotifications = async (userId) => {
  try {
    const notifications = await Notification.findAll({
      where: { user_id: userId },
      order: [['created_at', 'DESC']]
    });
    
    return notifications;
  } catch (error) {
    console.error('Error getting user notifications:', error);
    return [];
  }
};
