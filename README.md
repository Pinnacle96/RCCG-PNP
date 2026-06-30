# RCCG Prince and Princess Parish Website & Church Management System

A professional full-stack church website with integrated church management system (ChMS) built for shared hosting (cPanel).

## Table of Contents
- [Project Overview](#project-overview)
- [Technology Stack](#technology-stack)
- [Features](#features)
  - [Public Website](#public-website)
  - [Admin Panel (CMS)](#admin-panel-cms)
  - [Member Portal](#member-portal)
- [Folder Structure](#folder-structure)
- [Installation](#installation)
  - [Prerequisites](#prerequisites)
  - [Steps](#steps)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Admin Panel](#admin-panel-usage)
  - [Member Portal](#member-portal-usage)
- [Security](#security)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [License](#license)

## Project Overview

**Organization:** Redeemed Christian Church of God — Prince and Princess Parish

**Type:** Full-Stack Church Website + Integrated Church Management System (ChMS)

**Hosting Environment:** Shared Hosting (cPanel)

### Core Goals
- Establish a professional online presence for the parish
- Enable online giving, event registration, and sermon access
- Provide a complete backend for church administration
- Offer a member self-service portal
- Automate attendance tracking, finances, and communications

## Technology Stack

### Backend
- Language: PHP 8.1+
- Database: MySQL 8.0+
- Database Access: PDO with prepared statements
- Architecture: Custom lightweight MVC framework
- Password Hashing: `password_hash()` / `password_verify()` (bcrypt)
- Email: PHPMailer via Composer
- PDF Generation: DomPDF via Composer
- Excel Export: PhpSpreadsheet via Composer

### Frontend
- Markup: HTML5 (semantic)
- Styling: Tailwind CSS
- Interactivity: Vanilla JavaScript (ES6+)
- Icons: Tabler Icons
- Charts: Chart.js
- Rich Text Editor: TinyMCE
- Calendar: FullCalendar.js
- Date Picker: Flatpickr
- Data Tables: DataTables.js
- Notifications: SweetAlert2 + Toastify.js
- Media Player: Plyr.js

## Features

### Public Website
- Home page with hero, welcome message, latest sermons, events, ministries, announcements, and gallery
- About page with church history, leadership, beliefs
- Sermon archive with filtering, search, and series
- Event calendar with registration
- Ministries directory
- Online giving via Paystack/Flutterwave
- Prayer request wall
- Blog
- Photo gallery
- Contact page
- Live stream page
- Membership application page

### Admin Panel (CMS)
- Dashboard with KPI cards and charts
- Member management (add/edit/view, import/export, bulk actions)
- Sermon management (audio/video upload, series)
- Event management (registration, check-in)
- Ministry management
- Cell group management
- Attendance marking and reports
- Giving management and reports
- Prayer request management
- Blog management
- Gallery management
- Announcements
- Communications (email/SMS)
- Reports
- Contacts
- User management
- Site settings
- Audit log

### Member Portal
- Personal dashboard
- Profile management
- Giving history
- Attendance history
- Cell group information
- Ministry involvement

## Folder Structure

```
/                                 # cPanel home (NOT web root)
├── public_html/                   # Web root (everything lives here)
│   ├── index.php                  # Front controller (single entry point)
│   ├── .htaccess                  # Rewrite rules + security rules
│   ├── assets/                    # Public assets
│   │   ├── css/                   # Stylesheets
│   │   ├── js/                    # JavaScript files
│   │   ├── images/                # Images
│   │   └── fonts/                 # Fonts
│   ├── uploads/                   # Uploaded files
│   │   ├── .htaccess              # Blocks PHP execution in uploads
│   │   ├── sermons/               # Audio files
│   │   ├── events/                # Event banners
│   │   ├── gallery/               # Gallery photos
│   │   ├── members/               # Profile photos
│   │   ├── blog/                  # Blog cover images
│   │   └── documents/             # Downloadable PDFs
│   └── app/                       # Protected application code
│       ├── .htaccess              # Blocks all direct access to app/
│       ├── Config/                # Configuration files
│       ├── Core/                  # Core framework classes
│       ├── Models/                # Database models
│       ├── Controllers/           # Controllers
│       ├── Views/                 # View templates
│       ├── routes/                # Route definitions
│       ├── migrations/            # Database migrations
│       └── vendor/                # Composer packages
└── logs/                          # Optional: error logs (outside web root on some hosts)
```

## Installation

### Prerequisites
- PHP 8.1+
- MySQL 8.0+
- Apache with mod_rewrite enabled
- Composer
- Git

### Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Pinnacle96/RCCG-PNP.git
   cd RCCG-PNP
   ```

2. **Install dependencies:**
   ```bash
   cd app
   composer install
   ```

3. **Configure environment:**
   - Copy `app/Config/config.local.example.php` to `app/Config/config.php`
   - Edit `app/Config/config.php` and set your database credentials, BASE_URL, and other settings

4. **Create database:**
   - Create a MySQL database
   - Import the migrations from `app/migrations/`

5. **Seed initial settings (optional):**
   ```bash
   php app/Console/seed_settings.php
   ```

6. **Set permissions:**
   - Make sure the `uploads/` directory is writable by the web server
   ```bash
   chmod 755 uploads
   chmod 644 uploads/.htaccess
   ```

7. **Visit the site:**
   - Open your browser and navigate to your BASE_URL
   - The first user registered will automatically become a super admin

## Configuration

Key configuration settings in `app/Config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

define('BASE_URL', 'https://www.yourchurchdomain.org');
define('APP_PATH', __DIR__ . '/../');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');
```

## Usage

### Admin Panel Usage
- URL: `/admin`
- Log in with your admin credentials
- Use the sidebar to navigate between sections
- Manage members, sermons, events, ministries, giving, attendance, etc.

### Member Portal Usage
- URL: `/portal`
- Log in with your member credentials
- View and update your profile
- View your giving history
- Check your attendance record
- See your cell group information
- View your ministry involvements

## Security

This project uses multiple layers of security to protect your data:
- **HTTPS:** Always use HTTPS in production
- **Protected directories:** `.htaccess` files block direct access to sensitive code
- **Passwords:** Bcrypt hashing with salt
- **CSRF protection:** Cross-site request forgery tokens on all forms
- **XSS protection:** All output is escaped using `Helpers::escape()`
- **SQL injection prevention:** PDO prepared statements
- **File upload security:** Strict file type validation + no PHP execution in uploads
- **Audit log:** Tracks all administrative actions

## Deployment

### Shared Hosting (cPanel)
1. Upload all files to your `public_html/` directory
2. Create a MySQL database and user in cPanel
3. Import the database tables
4. Update `app/Config/config.php` with your database credentials
5. Set correct file permissions (755 for directories, 644 for files)
6. Verify that mod_rewrite is enabled

## Contributing

We welcome contributions! Please fork the repository and create a pull request with your changes.

## License

This project is proprietary and belongs to RCCG Prince and Princess Parish.
