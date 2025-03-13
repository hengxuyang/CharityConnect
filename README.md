# CharityConnect

CharityConnect is a web application that connects donors with charities, facilitating the donation of items to organizations in need. The platform allows charities to create donation requests for specific items, and donors can browse and fulfill these requests.

## Features

### For Donors
- Browse approved charities and their donation requests
- Filter donation requests by category and type (drop-off/pickup)
- Make donations to fulfill specific charity requests
- Track donation history and status
- Receive notifications about donation updates

### For Charities
- Register as a charity (requires admin approval)
- Create and manage donation requests
- Track inventory of received donations
- Manage charity profile and contact information
- Send thank-you messages to donors

### For Administrators
- Approve or reject charity registrations
- Manage categories and items
- Monitor all donations and requests
- User management

## Tech Stack

- **Framework**: Laravel 10
- **Frontend**: Blade templates with Tailwind CSS
- **Database**: MySQL
- **Authentication**: Laravel Breeze

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL
- Node.js and NPM

### Setup Instructions

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/charityconnect.git
   cd charityconnect
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install and compile frontend assets:
   ```bash
   npm install
   npm run dev
   ```

4. Create a copy of the .env file:
   ```bash
   cp .env.example .env
   ```

5. Generate an application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database in the .env file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=charityconnect
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. Run database migrations and seed initial data:
   ```bash
   php artisan migrate --seed
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

9. Visit `http://localhost:8000` in your browser to access the application.

## User Roles

### Public User (Donor)
- Can browse charities and donation requests
- Can make donations after registration/login
- Can track their donation history

### Charity
- Can create and manage donation requests
- Can manage inventory
- Can update charity information

### Administrator
- Has full access to all features
- Can approve/reject charity registrations
- Can manage categories and items

## Default Login Credentials

### Administrator
- Email: admin@example.com
- Password: password

### Charity
- Email: charity@example.com
- Password: password

### Donor
- Email: donor@example.com
- Password: password

## Project Structure

- `app/Models` - Contains all the database models
- `app/Http/Controllers` - Contains all the controllers
- `app/Http/Middleware` - Contains custom middleware for role-based access
- `resources/views` - Contains all the Blade templates
  - `resources/views/charities` - Charity-related views
  - `resources/views/requests` - Donation request views
  - `resources/views/donations` - Donation views
  - `resources/views/admin` - Admin dashboard views

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Acknowledgements

- [Laravel](https://laravel.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Laravel Breeze](https://github.com/laravel/breeze)
