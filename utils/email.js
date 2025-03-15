const nodemailer = require('nodemailer');
const dotenv = require('dotenv');

dotenv.config();

// Create reusable transporter object using SMTP transport
const transporter = nodemailer.createTransport({
  service: process.env.EMAIL_SERVICE,
  auth: {
    user: process.env.EMAIL_USER,
    pass: process.env.EMAIL_PASS
  }
});

// Function to send verification email
exports.sendVerificationEmail = async (email, token) => {
  const verificationLink = `${process.env.BASE_URL || 'http://localhost:3000'}/auth/verify-email?token=${token}`;
  
  const mailOptions = {
    from: `"CharityConnect" <${process.env.EMAIL_USER}>`,
    to: email,
    subject: 'Email Verification - CharityConnect',
    html: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #4a6f8a;">Welcome to CharityConnect!</h2>
        <p>Thank you for registering. Please verify your email address by clicking the link below:</p>
        <p>
          <a href="${verificationLink}" style="display: inline-block; background-color: #4a6f8a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Verify Email
          </a>
        </p>
        <p>If the button above doesn't work, you can also copy and paste the following link into your browser:</p>
        <p>${verificationLink}</p>
        <p>This link will expire in 24 hours.</p>
        <p>Thank you,<br>The CharityConnect Team</p>
      </div>
    `
  };

  try {
    const info = await transporter.sendMail(mailOptions);
    console.log('Verification email sent:', info.messageId);
    return true;
  } catch (error) {
    console.error('Error sending verification email:', error);
    return false;
  }
};

// Function to send password reset email
exports.sendPasswordResetEmail = async (email, token) => {
  const resetLink = `${process.env.BASE_URL || 'http://localhost:3000'}/auth/reset-password?token=${token}`;
  
  const mailOptions = {
    from: `"CharityConnect" <${process.env.EMAIL_USER}>`,
    to: email,
    subject: 'Password Reset - CharityConnect',
    html: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #4a6f8a;">Password Reset Request</h2>
        <p>You requested a password reset. Please click the link below to reset your password:</p>
        <p>
          <a href="${resetLink}" style="display: inline-block; background-color: #4a6f8a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Reset Password
          </a>
        </p>
        <p>If the button above doesn't work, you can also copy and paste the following link into your browser:</p>
        <p>${resetLink}</p>
        <p>This link will expire in 1 hour.</p>
        <p>If you did not request a password reset, please ignore this email or contact support.</p>
        <p>Thank you,<br>The CharityConnect Team</p>
      </div>
    `
  };

  try {
    const info = await transporter.sendMail(mailOptions);
    console.log('Password reset email sent:', info.messageId);
    return true;
  } catch (error) {
    console.error('Error sending password reset email:', error);
    return false;
  }
};

// Function to send notification email
exports.sendNotificationEmail = async (email, subject, message) => {
  const mailOptions = {
    from: `"CharityConnect" <${process.env.EMAIL_USER}>`,
    to: email,
    subject: subject,
    html: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #4a6f8a;">${subject}</h2>
        <p>${message}</p>
        <p>Thank you,<br>The CharityConnect Team</p>
      </div>
    `
  };

  try {
    const info = await transporter.sendMail(mailOptions);
    console.log('Notification email sent:', info.messageId);
    return true;
  } catch (error) {
    console.error('Error sending notification email:', error);
    return false;
  }
};

// Function to send thank you email to donor
exports.sendThankYouEmail = async (email, charityName, donationDetails) => {
  const mailOptions = {
    from: `"CharityConnect" <${process.env.EMAIL_USER}>`,
    to: email,
    subject: `Thank You for Your Donation to ${charityName}`,
    html: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #4a6f8a;">Thank You for Your Donation!</h2>
        <p>Dear Donor,</p>
        <p>${charityName} would like to express their sincere gratitude for your generous donation:</p>
        <div style="background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0;">
          <p><strong>Donation Details:</strong></p>
          <p>${donationDetails}</p>
        </div>
        <p>Your contribution makes a significant difference in our community and helps us continue our mission.</p>
        <p>Thank you for your support!</p>
        <p>Sincerely,<br>${charityName}<br>via CharityConnect</p>
      </div>
    `
  };

  try {
    const info = await transporter.sendMail(mailOptions);
    console.log('Thank you email sent:', info.messageId);
    return true;
  } catch (error) {
    console.error('Error sending thank you email:', error);
    return false;
  }
};
