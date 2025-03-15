# CharityConnect

CharityConnect is a web application that connects donors with charities to facilitate donations of needed items. The platform allows charities to register, create donation requests, and manage inventory, while donors can browse requests and make donations.

## Features

- User Authentication & Role Management
  - User Registration & Login (Admins, Charities, Donors)
  - Role-Based Access Control
  - Email Verification
  - Password Reset

- Charity Organization Management
  - Charity Registration & Approval
  - Profile Management
  - Status Tracking

- Donation Request Management
  - Create and Manage Donation Requests
  - Specify Drop-off or Pickup Options
  - Track Request Status

- Donor Features
  - Browse Available Donation Requests
  - Search & Filter by Category
  - Track Donations

- Inventory Management
  - Track Stock Levels
  - Automatic Updates
  - Low Stock Alerts

- Notification System
  - Email Notifications
  - Thank You Messages

## User Interfaces

### Admin Interface
- **Dashboard**: Overview of system activities, statistics, and recent actions
- **Charities Management**: Approve/reject charity applications and view details
- **User Management**: Manage all users in the system
- **Categories Management**: Create and manage item categories
- **Statistics**: View system-wide statistics and analytics

### Charity Interface
- **Dashboard**: Overview of charity activities, statistics, and quick actions
- **Profile Management**: Update charity information and manage users
- **Donation Requests**: Create, view, and manage donation requests
- **Inventory Management**: Track stock levels and manage inventory items
- **Donations**: View and manage incoming donations
- **Thank You Messages**: Send thank you notes to donors

### Donor Interface
- **Dashboard**: Overview of donation activities and available requests
- **Browse Requests**: Search and filter donation requests from charities
- **Charity Profiles**: View detailed information about charities
- **Donation Management**: Track and manage donation commitments
- **Notifications**: Receive updates about donations and charity activities
- **Profile Management**: Update personal information and preferences

## Technologies Used

- **Backend**: Node.js, Express.js
- **Database**: MySQL, Sequelize ORM
- **Frontend**: EJS, Bootstrap 5, JavaScript
- **Authentication**: JWT, bcrypt
- **Email**: Nodemailer
- **File Upload**: Multer

## Prerequisites

- Node.js (v14 or higher)
- MySQL (v5.7 or higher)

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/hengxuyang/CharityConnect.git
   cd charityconnect
   ```

2. Install dependencies:
   ```
   npm install
   ```

3. Create a MySQL database:
   ```
   CREATE DATABASE charity_connect;
   ```

4. Configure environment variables:
   - Rename `.env.example` to `.env` (or create a new `.env` file)
   - Update the database connection details and other settings

5. Initialize the database:
   ```
   npm node config/init-db.js
   ```

## Running the Application

### Development Mode

```
npm run dev
```

This will start the application with nodemon, which automatically restarts the server when changes are detected.

### Production Mode

```
npm start
```

The application will be available at http://localhost:3000

## Default Users

After initializing the database, the following users will be created:

- **Admin**
  - Email: admin@example.com
  - Password: admin123

- **Charity**
  - Email: charity@example.com
  - Password: charity123

- **Donor**
  - Email: donor@example.com
  - Password: donor123

## Project Structure

```
charityconnect/
├── config/             # Configuration files
├── controllers/        # Route controllers
├── middleware/         # Custom middleware
├── models/             # Sequelize models
├── public/             # Static files (CSS, JS, images)
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   └── uploads/        # Uploaded files
├── routes/             # Express routes
├── utils/              # Utility functions
├── views/              # EJS templates
│   ├── admin/          # Admin interface views
│   ├── auth/           # Authentication views
│   ├── charity/        # Charity interface views
│   ├── donor/          # Donor interface views
│   └── partials/       # Reusable view components
├── .env                # Environment variables
├── app.js              # Main application file
└── package.json        # Project dependencies
```

## Views Structure

### Admin Views
- `dashboard.ejs` - Main admin dashboard
- `charities.ejs` - List of charity organizations
- `charity-details.ejs` - Detailed view of a charity
- `users.ejs` - User management
- `categories.ejs` - Category management
- `statistics.ejs` - System statistics and analytics

### Charity Views
- `dashboard.ejs` - Main charity dashboard
- `profile.ejs` - Charity profile management
- `requests.ejs` - List of donation requests
- `create-request.ejs` - Form to create a new request
- `request-details.ejs` - Detailed view of a request
- `inventory.ejs` - Inventory management
- `donations.ejs` - List of donations

### Donor Views
- `dashboard.ejs` - Main donor dashboard
- `browse-requests.ejs` - Browse donation requests
- `request-details.ejs` - Detailed view of a request
- `my-donations.ejs` - List of donor's donations
- `charity-details.ejs` - Detailed view of a charity
- `notifications.ejs` - Notification center
- `profile.ejs` - Donor profile management

### Authentication Views
- `login.ejs` - Login form
- `register.ejs` - Registration form
- `forgot-password.ejs` - Forgot password form
- `reset-password.ejs` - Reset password form
- `verify-email.ejs` - Email verification page

## License

This project is licensed under the MIT License - see the LICENSE file for details.
