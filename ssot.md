# RCCG PRINCE AND PRINCESS PARISH
## Project Source of Truth (SOT)
### Version 1.1 — Shared Hosting Compatible

---

## TABLE OF CONTENTS

1. Project Overview
2. Technology Stack
3. Project Folder Structure (Shared Hosting)
4. Security Strategy for Shared Hosting
5. Database Architecture
6. Frontend Pages (Public)
7. Backend Pages (Admin/CMS)
8. Member Portal Pages
9. Features & Functionality Specification
10. API Endpoints (AJAX)
11. URL Routing (Clean URLs)
12. Security Architecture
13. UI/UX Design System
14. Third-Party Integrations
15. Development Phases & Milestones

---

## 1. PROJECT OVERVIEW

**Project Name:** RCCG Prince and Princess Parish Website & Church Management System
**Type:** Full-Stack Church Website + Integrated Church Management System (ChMS)
**Organization:** Redeemed Christian Church of God — Prince and Princess Parish
**Primary Audience:** Members, Visitors, Church Administration
**Hosting Environment:** Shared Hosting (cPanel) — web root is `public_html`

### Core Goals
- Establish a professional online presence for the parish
- Enable online giving, event registration, and sermon access
- Provide a complete backend for church administration
- Offer a member self-service portal
- Automate attendance tracking, finances, and communications

---

## 2. TECHNOLOGY STACK

### Backend
| Component | Technology |
|---|---|
| Language | PHP 8.1+ |
| Database | MySQL 8.0+ |
| Database Access | PDO with prepared statements |
| Architecture | MVC (custom lightweight, no framework) |
| URL Routing | Custom PHP router via .htaccess mod_rewrite |
| Authentication | PHP Sessions + CSRF tokens |
| Password Hashing | `password_hash()` / `password_verify()` (bcrypt) |
| File Uploads | PHP native + strict validation |
| Email | PHPMailer via Composer |
| PDF Generation | DomPDF via Composer |
| Excel Export | PhpSpreadsheet via Composer |

### Frontend
| Component | Technology |
|---|---|
| Markup | HTML5 (semantic) |
| Styling | Tailwind CSS build |
| Interactivity | Vanilla JavaScript (ES6+) |
| Async Requests | AJAX via Fetch API |
| Icons | Tabler Icons (SVG CDN) |
| Charts | Chart.js (CDN) |
| Rich Text Editor | TinyMCE (CDN) |
| Calendar | FullCalendar.js (CDN) |
| Date Picker | Flatpickr (CDN) |
| Data Tables | DataTables.js (CDN) |
| Notifications | SweetAlert2 + Toastify.js |
| Media Player | Plyr.js (CDN) |

---

## 3. PROJECT FOLDER STRUCTURE (SHARED HOSTING)

The key principle: sensitive PHP application files live **inside** `public_html` but are protected by `.htaccess` rules that deny direct browser access. Only `index.php`, assets, and intentional upload endpoints are publicly reachable.

```
/home/{cpanel_username}/                  ← cPanel home (NOT web root)
│
├── public_html/                          ← Web root (everything lives here)
│   │
│   ├── index.php                         ← Front controller (single entry point)
│   ├── .htaccess                         ← Rewrite rules + security rules
│   │
│   ├── assets/                           ← PUBLIC: CSS, JS, fonts, static images
│   │   ├── css/
│   │   │   └── custom.css
│   │   ├── js/
│   │   │   ├── app.js
│   │   │   ├── ajax.js
│   │   │   ├── admin.js
│   │   │   └── member.js
│   │   ├── images/
│   │   │   ├── logo/
│   │   │   ├── banners/
│   │   │   └── defaults/
│   │   └── fonts/
│   │
│   ├── uploads/                          ← PUBLIC: user-uploaded files (served directly)
│   │   ├── sermons/                      ← audio files
│   │   ├── events/                       ← event banners
│   │   ├── gallery/                      ← gallery photos
│   │   ├── members/                      ← profile photos
│   │   ├── blog/                         ← blog cover images
│   │   └── documents/                    ← downloadable PDFs
│   │   └── .htaccess                     ← DENY PHP execution in uploads folder
│   │
│   └── app/                              ← PROTECTED: application source code
│       ├── .htaccess                     ← Deny all direct HTTP access to /app/
│       │
│       ├── Config/
│       │   ├── config.php                ← DB credentials, constants, site config
│       │   ├── database.php              ← PDO singleton connection class
│       │   └── mailer.php                ← PHPMailer SMTP config
│       │
│       ├── Core/
│       │   ├── Router.php                ← URL routing engine
│       │   ├── Controller.php            ← Base controller class
│       │   ├── Model.php                 ← Base model (PDO wrapper methods)
│       │   ├── View.php                  ← Template renderer
│       │   ├── Auth.php                  ← Session + role helpers
│       │   ├── Validator.php             ← Input validation rules
│       │   ├── Uploader.php              ← File upload handler
│       │   ├── Mailer.php                ← Email sending wrapper
│       │   └── Helpers.php               ← Utility functions (slug, format, etc.)
│       │
│       ├── Models/
│       │   ├── UserModel.php
│       │   ├── MemberModel.php
│       │   ├── SermonModel.php
│       │   ├── EventModel.php
│       │   ├── MinistryModel.php
│       │   ├── GivingModel.php
│       │   ├── AttendanceModel.php
│       │   ├── PrayerModel.php
│       │   ├── AnnouncementModel.php
│       │   ├── GalleryModel.php
│       │   ├── BlogModel.php
│       │   ├── ContactModel.php
│       │   ├── SettingsModel.php
│       │   ├── CellGroupModel.php
│       │   ├── NotificationModel.php
│       │   └── ReportModel.php
│       │
│       ├── Controllers/
│       │   ├── Frontend/
│       │   │   ├── HomeController.php
│       │   │   ├── AboutController.php
│       │   │   ├── SermonController.php
│       │   │   ├── EventController.php
│       │   │   ├── MinistryController.php
│       │   │   ├── GiveController.php
│       │   │   ├── PrayerController.php
│       │   │   ├── BlogController.php
│       │   │   ├── GalleryController.php
│       │   │   └── ContactController.php
│       │   │
│       │   ├── Admin/
│       │   │   ├── DashboardController.php
│       │   │   ├── MemberController.php
│       │   │   ├── SermonController.php
│       │   │   ├── EventController.php
│       │   │   ├── MinistryController.php
│       │   │   ├── GivingController.php
│       │   │   ├── AttendanceController.php
│       │   │   ├── PrayerController.php
│       │   │   ├── BlogController.php
│       │   │   ├── GalleryController.php
│       │   │   ├── AnnouncementController.php
│       │   │   ├── ReportController.php
│       │   │   ├── CellGroupController.php
│       │   │   ├── NotificationController.php
│       │   │   └── SettingsController.php
│       │   │
│       │   ├── Member/
│       │   │   ├── DashboardController.php
│       │   │   ├── ProfileController.php
│       │   │   ├── GivingController.php
│       │   │   ├── AttendanceController.php
│       │   │   └── CellGroupController.php
│       │   │
│       │   └── Auth/
│       │       ├── LoginController.php
│       │       ├── RegisterController.php
│       │       └── PasswordController.php
│       │
│       ├── Views/
│       │   ├── layouts/
│       │   │   ├── public.php            ← Public site layout (header/footer wrap)
│       │   │   ├── admin.php             ← Admin panel layout (sidebar + topbar)
│       │   │   ├── member.php            ← Member portal layout
│       │   │   └── auth.php              ← Auth pages layout (centered card)
│       │   │
│       │   ├── partials/
│       │   │   ├── nav.php               ← Public navigation
│       │   │   ├── footer.php            ← Public footer
│       │   │   ├── admin-sidebar.php
│       │   │   ├── admin-topbar.php
│       │   │   ├── member-sidebar.php
│       │   │   └── flash-alerts.php      ← Success/error messages
│       │   │
│       │   ├── frontend/
│       │   │   ├── home.php
│       │   │   ├── about.php
│       │   │   ├── sermons/
│       │   │   │   ├── index.php
│       │   │   │   ├── single.php
│       │   │   │   └── series.php
│       │   │   ├── events/
│       │   │   │   ├── index.php
│       │   │   │   └── single.php
│       │   │   ├── ministries/
│       │   │   │   ├── index.php
│       │   │   │   └── single.php
│       │   │   ├── give.php
│       │   │   ├── give-success.php
│       │   │   ├── prayer.php
│       │   │   ├── blog/
│       │   │   │   ├── index.php
│       │   │   │   └── single.php
│       │   │   ├── gallery/
│       │   │   │   ├── index.php
│       │   │   │   └── album.php
│       │   │   ├── contact.php
│       │   │   ├── livestream.php
│       │   │   ├── join.php
│       │   │   ├── search.php
│       │   │   └── 404.php
│       │   │
│       │   ├── admin/
│       │   │   ├── dashboard.php
│       │   │   ├── members/
│       │   │   │   ├── index.php
│       │   │   │   ├── add.php
│       │   │   │   ├── edit.php
│       │   │   │   └── view.php
│       │   │   ├── sermons/
│       │   │   │   ├── index.php
│       │   │   │   ├── form.php          ← reused for add and edit
│       │   │   │   └── series.php
│       │   │   ├── events/
│       │   │   │   ├── index.php
│       │   │   │   ├── form.php
│       │   │   │   └── registrations.php
│       │   │   ├── ministries/
│       │   │   │   ├── index.php
│       │   │   │   └── form.php
│       │   │   ├── cellgroups/
│       │   │   │   ├── index.php
│       │   │   │   └── form.php
│       │   │   ├── attendance/
│       │   │   │   ├── mark.php
│       │   │   │   └── reports.php
│       │   │   ├── giving/
│       │   │   │   ├── index.php
│       │   │   │   └── reports.php
│       │   │   ├── prayer/
│       │   │   │   └── index.php
│       │   │   ├── blog/
│       │   │   │   ├── index.php
│       │   │   │   └── form.php
│       │   │   ├── gallery/
│       │   │   │   └── index.php
│       │   │   ├── announcements/
│       │   │   │   └── index.php
│       │   │   ├── communications/
│       │   │   │   └── index.php
│       │   │   ├── reports/
│       │   │   │   └── index.php
│       │   │   ├── contacts/
│       │   │   │   └── index.php
│       │   │   ├── users/
│       │   │   │   └── index.php
│       │   │   ├── settings/
│       │   │   │   └── index.php
│       │   │   └── audit-log/
│       │   │       └── index.php
│       │   │
│       │   ├── member/
│       │   │   ├── dashboard.php
│       │   │   ├── profile.php
│       │   │   ├── giving.php
│       │   │   ├── attendance.php
│       │   │   ├── cellgroup.php
│       │   │   └── ministry.php
│       │   │
│       │   └── auth/
│       │       ├── login.php
│       │       ├── register.php
│       │       ├── forgot-password.php
│       │       └── reset-password.php
│       │
│       ├── routes/
│       │   ├── web.php                   ← All page route definitions
│       │   └── api.php                   ← All AJAX/API route definitions
│       │
│       ├── migrations/
│       │   ├── 001_create_users.sql
│       │   ├── 002_create_members.sql
│       │   ├── 003_create_sermons.sql
│       │   └── ...all table SQL files
│       │
│       └── vendor/                       ← Composer packages (PHPMailer, DomPDF, etc.)
│           └── autoload.php
│
└── logs/                                 ← Optional: error logs (outside web root on some hosts)
```

---

## 4. SECURITY STRATEGY FOR SHARED HOSTING

Since all files are inside `public_html`, two `.htaccess` files do the heavy lifting:

### 4.1 Root `.htaccess` — `public_html/.htaccess`
This handles clean URLs AND blocks direct access to the `app/` folder.

```apache
Options -Indexes
RewriteEngine On
RewriteBase /

# Block direct access to the entire app/ directory
RewriteRule ^app/ - [F,L]

# Allow real files and directories (assets, uploads)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Route everything else through index.php
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
<IfModule mod_headers.c>
    Header always set X-Frame-Options "DENY"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Block access to sensitive file types anywhere
<FilesMatch "\.(env|sql|log|sh|bak|git|composer\.json|composer\.lock)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 4.2 App Folder `.htaccess` — `public_html/app/.htaccess`
Absolute lockdown. No browser can directly load any file in `app/`.

```apache
Order deny,allow
Deny from all
```

### 4.3 Uploads Folder `.htaccess` — `public_html/uploads/.htaccess`
Uploaded files are served directly BUT PHP execution is completely disabled here — prevents anyone uploading a disguised PHP script.

```apache
# Prevent PHP execution in uploads
<FilesMatch "\.ph(p[2-7]?|tml)$">
    Order deny,allow
    Deny from all
</FilesMatch>

# Only allow specific safe file types
<FilesMatch "\.(jpg|jpeg|png|gif|webp|mp3|m4a|pdf|docx)$">
    Order allow,deny
    Allow from all
</FilesMatch>
```

### 4.4 Config File Protection
Store database credentials in `app/Config/config.php` — never in a `.env` file (`.htaccess` blocks it anyway, but PHP files are safer on shared hosts since they are executed, not served as text).

```php
// app/Config/config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cpanelusername_rccgdb');
define('DB_USER', 'cpanelusername_dbuser');
define('DB_PASS', 'StrongPasswordHere');
define('DB_CHARSET', 'utf8mb4');

define('BASE_URL', 'https://www.rccgprinceandprincess.org');
define('APP_PATH', __DIR__ . '/../');    // resolves to public_html/app/
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');
```

### 4.5 index.php — Front Controller
The only publicly executable PHP file. All requests flow through here.

```php
<?php
// public_html/index.php

define('ROOT', __DIR__);
define('APP', ROOT . '/app');

// Block direct script access
if (php_sapi_name() === 'cli') exit('No CLI access');

require_once APP . '/Config/config.php';
require_once APP . '/vendor/autoload.php';
require_once APP . '/Core/Router.php';
require_once APP . '/routes/web.php';
require_once APP . '/routes/api.php';

$router = new Router();
$router->dispatch();
```

---

## 5. DATABASE ARCHITECTURE

### 5.1 Table: `users`
```sql
CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id       INT UNSIGNED NULL,
    email           VARCHAR(150) UNIQUE NOT NULL,
    password        VARCHAR(255) NOT NULL,
    role            ENUM('super_admin','admin','pastor','deacon','cell_leader','member') DEFAULT 'member',
    is_active       TINYINT(1) DEFAULT 1,
    last_login      DATETIME NULL,
    remember_token  VARCHAR(100) NULL,
    reset_token     VARCHAR(100) NULL,
    reset_expires   DATETIME NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 5.2 Table: `members`
```sql
CREATE TABLE members (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_code         VARCHAR(20) UNIQUE NOT NULL,
    first_name          VARCHAR(80) NOT NULL,
    last_name           VARCHAR(80) NOT NULL,
    middle_name         VARCHAR(80) NULL,
    gender              ENUM('male','female') NOT NULL,
    date_of_birth       DATE NULL,
    phone               VARCHAR(20) NULL,
    alt_phone           VARCHAR(20) NULL,
    email               VARCHAR(150) NULL,
    address             TEXT NULL,
    state_of_origin     VARCHAR(80) NULL,
    occupation          VARCHAR(100) NULL,
    marital_status      ENUM('single','married','divorced','widowed') NULL,
    spouse_name         VARCHAR(160) NULL,
    wedding_anniversary DATE NULL,
    membership_type     ENUM('full','associate','worker','junior') DEFAULT 'full',
    membership_status   ENUM('active','inactive','transferred','deceased') DEFAULT 'active',
    join_date           DATE NULL,
    baptism_date        DATE NULL,
    water_baptized      TINYINT(1) DEFAULT 0,
    holy_ghost_baptized TINYINT(1) DEFAULT 0,
    profile_photo       VARCHAR(255) NULL,
    cell_group_id       INT UNSIGNED NULL,
    ministry_id         INT UNSIGNED NULL,
    emergency_contact   VARCHAR(160) NULL,
    emergency_phone     VARCHAR(20) NULL,
    notes               TEXT NULL,
    created_by          INT UNSIGNED NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 5.3 Table: `sermons`
```sql
CREATE TABLE sermons (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) UNIQUE NOT NULL,
    preacher        VARCHAR(160) NOT NULL,
    scripture_ref   VARCHAR(255) NULL,
    series_id       INT UNSIGNED NULL,
    description     TEXT NULL,
    audio_file      VARCHAR(255) NULL,
    video_url       VARCHAR(500) NULL,
    thumbnail       VARCHAR(255) NULL,
    sermon_date     DATE NOT NULL,
    sermon_type     ENUM('sunday','midweek','special','program') DEFAULT 'sunday',
    duration_mins   INT NULL,
    views           INT UNSIGNED DEFAULT 0,
    downloads       INT UNSIGNED DEFAULT 0,
    is_published    TINYINT(1) DEFAULT 1,
    is_featured     TINYINT(1) DEFAULT 0,
    tags            VARCHAR(500) NULL,
    created_by      INT UNSIGNED NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 5.4 Table: `sermon_series`
```sql
CREATE TABLE sermon_series (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    slug        VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    cover_image VARCHAR(255) NULL,
    start_date  DATE NULL,
    end_date    DATE NULL,
    is_active   TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.5 Table: `events`
```sql
CREATE TABLE events (
    id                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title                   VARCHAR(255) NOT NULL,
    slug                    VARCHAR(255) UNIQUE NOT NULL,
    description             TEXT NULL,
    short_description       VARCHAR(500) NULL,
    banner_image            VARCHAR(255) NULL,
    event_date              DATE NOT NULL,
    end_date                DATE NULL,
    start_time              TIME NULL,
    end_time                TIME NULL,
    venue                   VARCHAR(255) NULL,
    address                 TEXT NULL,
    requires_registration   TINYINT(1) DEFAULT 0,
    registration_limit      INT NULL,
    is_published            TINYINT(1) DEFAULT 1,
    is_featured             TINYINT(1) DEFAULT 0,
    created_by              INT UNSIGNED NULL,
    created_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 5.6 Table: `event_registrations`
```sql
CREATE TABLE event_registrations (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id    INT UNSIGNED NOT NULL,
    member_id   INT UNSIGNED NULL,
    name        VARCHAR(160) NOT NULL,
    email       VARCHAR(150) NULL,
    phone       VARCHAR(20) NULL,
    adults      INT DEFAULT 1,
    children    INT DEFAULT 0,
    status      ENUM('pending','confirmed','cancelled') DEFAULT 'confirmed',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.7 Table: `ministries`
```sql
CREATE TABLE ministries (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(160) NOT NULL,
    slug            VARCHAR(160) UNIQUE NOT NULL,
    description     TEXT NULL,
    short_desc      VARCHAR(500) NULL,
    leader_name     VARCHAR(160) NULL,
    leader_id       INT UNSIGNED NULL,
    meeting_schedule VARCHAR(255) NULL,
    meeting_venue   VARCHAR(255) NULL,
    contact_email   VARCHAR(150) NULL,
    contact_phone   VARCHAR(20) NULL,
    cover_image     VARCHAR(255) NULL,
    is_active       TINYINT(1) DEFAULT 1,
    display_order   INT DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.8 Table: `ministry_members`
```sql
CREATE TABLE ministry_members (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ministry_id INT UNSIGNED NOT NULL,
    member_id   INT UNSIGNED NOT NULL,
    role        VARCHAR(80) NULL,
    joined_date DATE NULL,
    is_active   TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_member_ministry (ministry_id, member_id)
);
```

### 5.9 Table: `cell_groups`
```sql
CREATE TABLE cell_groups (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(160) NOT NULL,
    zone            VARCHAR(100) NULL,
    leader_id       INT UNSIGNED NULL,
    co_leader_id    INT UNSIGNED NULL,
    meeting_day     ENUM('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NULL,
    meeting_time    TIME NULL,
    meeting_venue   VARCHAR(255) NULL,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.10 Table: `services`
```sql
CREATE TABLE services (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_type    ENUM('sunday_first','sunday_second','wednesday','friday','special','cell') DEFAULT 'sunday_first',
    service_date    DATE NOT NULL,
    theme           VARCHAR(255) NULL,
    preacher        VARCHAR(160) NULL,
    total_count     INT DEFAULT 0,
    men_count       INT DEFAULT 0,
    women_count     INT DEFAULT 0,
    children_count  INT DEFAULT 0,
    visitors_count  INT DEFAULT 0,
    offering_amount DECIMAL(12,2) DEFAULT 0.00,
    tithe_amount    DECIMAL(12,2) DEFAULT 0.00,
    notes           TEXT NULL,
    is_closed       TINYINT(1) DEFAULT 0,
    created_by      INT UNSIGNED NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.11 Table: `attendance`
```sql
CREATE TABLE attendance (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id      INT UNSIGNED NOT NULL,
    member_id       INT UNSIGNED NOT NULL,
    check_in_time   DATETIME NULL,
    method          ENUM('manual','qr','self') DEFAULT 'manual',
    marked_by       INT UNSIGNED NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attendance (service_id, member_id)
);
```

### 5.12 Table: `giving`
```sql
CREATE TABLE giving (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference_no    VARCHAR(50) UNIQUE NOT NULL,
    member_id       INT UNSIGNED NULL,
    giver_name      VARCHAR(160) NULL,
    giver_email     VARCHAR(150) NULL,
    giver_phone     VARCHAR(20) NULL,
    amount          DECIMAL(12,2) NOT NULL,
    currency        VARCHAR(5) DEFAULT 'NGN',
    giving_type     ENUM('tithe','offering','seed','project','welfare','mission','thanksgiving','vow','other') NOT NULL,
    giving_method   ENUM('cash','bank_transfer','pos','online','cheque') DEFAULT 'online',
    service_id      INT UNSIGNED NULL,
    payment_gateway VARCHAR(50) NULL,
    gateway_ref     VARCHAR(255) NULL,
    payment_status  ENUM('pending','success','failed','reversed') DEFAULT 'pending',
    description     VARCHAR(500) NULL,
    giving_date     DATE NOT NULL,
    recorded_by     INT UNSIGNED NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 5.13 Table: `prayer_requests`
```sql
CREATE TABLE prayer_requests (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id       INT UNSIGNED NULL,
    requester_name  VARCHAR(160) NOT NULL,
    email           VARCHAR(150) NULL,
    phone           VARCHAR(20) NULL,
    subject         VARCHAR(255) NOT NULL,
    request_text    TEXT NOT NULL,
    category        ENUM('healing','deliverance','finance','family','salvation','career','marriage','thanksgiving','others') DEFAULT 'others',
    is_private      TINYINT(1) DEFAULT 0,
    is_answered     TINYINT(1) DEFAULT 0,
    answered_note   TEXT NULL,
    status          ENUM('new','praying','answered','archived') DEFAULT 'new',
    prayer_count    INT DEFAULT 0,
    assigned_to     INT UNSIGNED NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 5.14 Table: `blog_posts`
```sql
CREATE TABLE blog_posts (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) UNIQUE NOT NULL,
    category_id     INT UNSIGNED NULL,
    author_id       INT UNSIGNED NULL,
    excerpt         TEXT NULL,
    body            LONGTEXT NOT NULL,
    cover_image     VARCHAR(255) NULL,
    tags            VARCHAR(500) NULL,
    views           INT UNSIGNED DEFAULT 0,
    is_published    TINYINT(1) DEFAULT 0,
    is_featured     TINYINT(1) DEFAULT 0,
    published_at    DATETIME NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 5.15 Table: `gallery_albums`
```sql
CREATE TABLE gallery_albums (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    slug        VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    cover_image VARCHAR(255) NULL,
    event_date  DATE NULL,
    is_published TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.16 Table: `gallery`
```sql
CREATE TABLE gallery (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    album_id    INT UNSIGNED NOT NULL,
    title       VARCHAR(255) NULL,
    file_path   VARCHAR(255) NOT NULL,
    file_type   ENUM('image','video') DEFAULT 'image',
    is_featured TINYINT(1) DEFAULT 0,
    sort_order  INT DEFAULT 0,
    created_by  INT UNSIGNED NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.17 Table: `announcements`
```sql
CREATE TABLE announcements (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(255) NOT NULL,
    body            TEXT NOT NULL,
    type            ENUM('general','urgent','event','pastoral') DEFAULT 'general',
    target          ENUM('all','members','workers','leaders') DEFAULT 'all',
    start_date      DATE NOT NULL,
    end_date        DATE NULL,
    is_active       TINYINT(1) DEFAULT 1,
    show_on_website TINYINT(1) DEFAULT 1,
    created_by      INT UNSIGNED NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.18 Table: `contacts`
```sql
CREATE TABLE contacts (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(160) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    phone       VARCHAR(20) NULL,
    subject     VARCHAR(255) NOT NULL,
    message     TEXT NOT NULL,
    ip_address  VARCHAR(45) NULL,
    is_read     TINYINT(1) DEFAULT 0,
    is_replied  TINYINT(1) DEFAULT 0,
    reply_text  TEXT NULL,
    replied_by  INT UNSIGNED NULL,
    replied_at  DATETIME NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.19 Table: `notifications`
```sql
CREATE TABLE notifications (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    type        VARCHAR(80) NOT NULL,
    title       VARCHAR(255) NOT NULL,
    message     TEXT NULL,
    link        VARCHAR(500) NULL,
    is_read     TINYINT(1) DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.20 Table: `site_settings`
```sql
CREATE TABLE site_settings (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_val TEXT NULL,
    group_name  VARCHAR(80) NULL,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 5.21 Table: `sms_emails_log`
```sql
CREATE TABLE sms_emails_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type        ENUM('email','sms') NOT NULL,
    recipient   VARCHAR(255) NOT NULL,
    subject     VARCHAR(255) NULL,
    body        TEXT NULL,
    status      ENUM('sent','failed','pending') DEFAULT 'pending',
    sent_by     INT UNSIGNED NULL,
    sent_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.22 Table: `audit_log`
```sql
CREATE TABLE audit_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NULL,
    action      VARCHAR(100) NOT NULL,
    module      VARCHAR(80) NULL,
    description TEXT NULL,
    ip_address  VARCHAR(45) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 6. FRONTEND PAGES (PUBLIC)

### PAGE 1: Home — `/`
**Sections:**
- Hero: full-width banner, parish name, tagline, service times, CTA buttons (Join Us / Watch Live / Give)
- Live Service Banner: conditionally shown when service is live (AJAX poll every 60s)
- Service Times: Sunday Worship service, Wednesday Bible Study, Friday Prayer Night
- Welcome Message: Pastor's note with photo
- Latest Sermon: most recent with Plyr.js player and play/download buttons
- Upcoming Events: next 3 events with date and Register button
- Ministries Overview: icon grid of active ministries
- Announcement Ticker: scrolling banner of active announcements
- Give Banner: online giving call-to-action
- Gallery Teaser: 6 recent gallery photos
- Blog Preview: 3 latest blog posts
- Newsletter Signup: email capture (AJAX)
- Map/Find Us: embedded Google Map with address

---

### PAGE 2: About — `/about`
**Sections:**
- Our Story: parish history
- RCCG Overview: denomination context with link to rccg.org
- Vision & Mission
- Core Values (icon cards)
- Meet the Leadership: Pastor, Associate Pastors, Deacons — photo, name, title, bio
- Church Beliefs: doctrinal statements (accordion)
- Join Us CTA

---

### PAGE 3: Sermons List — `/sermons`
**Features:**
- Filter bar: preacher, series, date range, type (AJAX)
- Search box (AJAX live search, debounced)
- Sort: newest, oldest, most viewed, most downloaded
- Sermon cards: thumbnail, title, preacher, date, duration, listen/download
- Pagination (AJAX)
- Sermon Series row at top

---

### PAGE 4: Single Sermon — `/sermons/{slug}`
**Features:**
- Plyr.js audio player
- YouTube/Vimeo video embed if video URL exists
- Details: title, preacher, scripture, series, date, tags
- Download button (increments counter via AJAX)
- View count (incremented on load via AJAX)
- Personal notes textarea (saves to localStorage)
- Related sermons (same series/preacher)
- Social share: WhatsApp, Facebook, Twitter/X, Copy Link

---

### PAGE 5: Sermon Series — `/sermons/series/{slug}`
- Series cover image and description
- All sermons in the series listed with player access

---

### PAGE 6: Events List — `/events`
**Features:**
- FullCalendar.js monthly view + list view toggle
- Category filter
- Upcoming events cards with date badge, venue, Register button
- Past events section

---

### PAGE 7: Single Event — `/events/{slug}`
**Features:**
- Banner image
- Date, time, venue, address
- Rich text description
- Registration form (AJAX): name, email, phone, adults, children
- Seat availability counter
- Add to Calendar (.ics file download)
- Share buttons
- Related events

---

### PAGE 8: Ministries List — `/ministries`
- Grid of ministry cards: icon, name, short description, meeting schedule, link to detail

---

### PAGE 9: Single Ministry — `/ministries/{slug}`
- Banner, full description
- Leadership profile
- Meeting schedule and venue
- Contact form for this ministry (AJAX)
- Ministry gallery
- Join Ministry form (AJAX)

---

### PAGE 10: Give — `/give`
**Features:**
- Giving type tabs: Tithe, Offering, Seed Faith, Special Project, Welfare, Missions
- Quick amount buttons + custom amount
- Giver info (pre-filled if logged in)
- Paystack/Flutterwave inline payment
- Bank transfer details section with reference number
- Giving history for logged-in members
- Anonymous giving option
- Scripture encouragement text

---

### PAGE 11: Giving Success — `/give/success`
- Receipt display: reference, amount, type, date
- Download PDF receipt button
- Share giving testimony option
- Link back to home

---

### PAGE 12: Prayer — `/prayer`
**Features:**
- Submit prayer request form (AJAX): name, email, subject, category, text, privacy toggle
- Public prayer wall (non-private requests) with "I'm Praying" button (AJAX counter)
- Answered prayers / testimonies section

---

### PAGE 13: Blog List — `/blog`
- Featured post hero
- Blog post grid with pagination
- Category filter sidebar
- Tag cloud
- Search (AJAX)
- Subscribe form

---

### PAGE 14: Single Blog Post — `/blog/{slug}`
- Full post content
- Author card
- Social share
- Comments section (with moderation)
- Related posts

---

### PAGE 15: Gallery Albums — `/gallery`
- Album grid with cover photo and photo count badge

---

### PAGE 16: Gallery Album — `/gallery/{slug}`
- Masonry/grid photo layout
- GLightbox or Fancybox lightbox
- Video thumbnails with play icon

---

### PAGE 17: Contact — `/contact`
- Contact form (AJAX + reCAPTCHA v3)
- Church address, phone, email, social links
- Google Maps embed
- Service times reference
- Office hours

---

### PAGE 18: Live Stream — `/livestream`
- YouTube Live embed (URL from settings)
- Countdown timer to next scheduled service
- Give during stream CTA

---

### PAGE 19: Join (Membership Application) — `/join`
- Multi-step form (Step 1: Personal Info → Step 2: Faith Background → Step 3: Ministry Interest)
- AJAX step validation
- Passport photo upload
- Confirmation email on submit
- Creates pending member record

---

### PAGE 20: Search Results — `/search`
- Searches sermons, events, blog posts, ministries, pages
- Grouped results by type

---

### AUTHENTICATION PAGES

| Page | URL |
|---|---|
| Login | `/login` |
| Register | `/register` |
| Forgot Password | `/forgot-password` |
| Reset Password | `/reset-password/{token}` |
| Logout | `/logout` |

---

## 7. BACKEND PAGES (ADMIN/CMS)

**Base URL:** `/admin` — requires authenticated admin/staff session

### ADMIN PAGE 1: Dashboard — `/admin`
- KPI cards: Total Members, Active Members, Today's Attendance, Monthly Giving, Pending Prayers, Unread Messages
- Weekly attendance bar chart (Chart.js — last 8 Sundays)
- Monthly giving line chart (last 12 months)
- Recent giving transactions table (last 10)
- Upcoming events list (next 5)
- New member registrations (last 7 days)
- Birthday alerts: members with birthdays this week
- Quick action buttons: Mark Attendance, Record Giving, Add Member, Send Announcement

---

### ADMIN PAGE 2: Members List — `/admin/members`
- DataTables.js: searchable, sortable, filterable
- Columns: Photo, Code, Name, Phone, Ministry, Cell Group, Status, Joined, Actions
- Filters: status, ministry, cell group, type
- Export: CSV, Excel, PDF
- Bulk actions: activate, deactivate, send email
- Import from Excel button

---

### ADMIN PAGE 3: Add Member — `/admin/members/add`
- Full member form (all fields)
- Profile photo upload with crop (Croppie.js)
- Auto-generate member code (RCCG-PP-XXXXX)
- Assign cell group and ministries
- Option to create login account
- Client-side + server-side validation

---

### ADMIN PAGE 4: Edit Member — `/admin/members/edit/{id}`
- Pre-populated form
- Change password section
- Account status toggle
- Tabs: Giving History, Attendance History, Ministry Memberships
- Print member card button

---

### ADMIN PAGE 5: View Member Profile — `/admin/members/view/{id}`
- Full profile display
- Attendance summary chart
- Giving summary chart
- Prayer requests history
- Event registrations

---

### ADMIN PAGE 6: Sermons List — `/admin/sermons`
- Table: title, preacher, date, type, views, downloads, published
- Filter by series, preacher, date range
- Add / Edit / Delete

---

### ADMIN PAGE 7: Add/Edit Sermon — `/admin/sermons/add` | `/admin/sermons/edit/{id}`
- Title, slug (auto), preacher, scripture
- Series dropdown
- TinyMCE description editor
- Audio upload (MP3/M4A) with AJAX progress bar
- Video URL input
- Thumbnail upload
- Date, type, duration, tags
- Featured + Published toggles
- SEO fields (meta title, meta description)

---

### ADMIN PAGE 8: Sermon Series — `/admin/sermons/series`
- CRUD: title, description, cover image, dates

---

### ADMIN PAGE 9: Events List — `/admin/events`
- Table: title, date, registrations count, status
- Calendar view tab
- Add / Edit / Delete

---

### ADMIN PAGE 10: Add/Edit Event — `/admin/events/add` | `/admin/events/edit/{id}`
- All event fields
- TinyMCE description
- Banner image upload
- Registration settings: toggle, capacity, fee
- SEO fields

---

### ADMIN PAGE 11: Event Registrations — `/admin/events/{id}/registrations`
- Table of registrations
- Check-in toggle per person
- Export Excel/PDF
- Send reminder email blast (AJAX)
- Print attendance list

---

### ADMIN PAGE 12: Ministries — `/admin/ministries`
- CRUD for ministries
- Ministry members sub-table per ministry

---

### ADMIN PAGE 13: Cell Groups — `/admin/cellgroups`
- CRUD: name, zone, leader, meeting day/time/venue
- Members list per group
- Assign/remove members

---

### ADMIN PAGE 14: Mark Attendance — `/admin/attendance`
- Service selector: date + type
- Live member search (AJAX) with checkbox mark
- Visitor head count fields (men, women, children, visitors)
- QR scanner mode (camera via jsQR.js)
- Running count display
- Close Service button (locks record)

---

### ADMIN PAGE 15: Attendance Reports — `/admin/attendance/reports`
- Date range filter
- Per-member attendance % score
- Service totals chart
- Ministry attendance breakdown
- Absentee list: members absent 3+ consecutive Sundays
- Export CSV, Excel, PDF

---

### ADMIN PAGE 16: Giving Transactions — `/admin/giving`
- Table: reference, giver, amount, type, method, date, status
- Filters: date, type, method, status
- Record manual giving (cash/POS form)
- Verify pending online payments (gateway API check via AJAX)
- Print receipt for any transaction
- Export Excel, PDF

---

### ADMIN PAGE 17: Giving Reports — `/admin/giving/reports`
- Summary cards: this week, month, year totals
- Monthly trend chart
- Giving by type pie chart
- Top givers table (with anonymization toggle)
- Per-member annual giving statement (PDF download)

---

### ADMIN PAGE 18: Prayer Requests — `/admin/prayer`
- Table: requester, category, date, status, privacy
- View full request
- Update status: New → Praying → Answered → Archived
- Assign to prayer team member
- Email reply to requester (AJAX)
- Answered note field

---

### ADMIN PAGE 19: Blog Posts — `/admin/blog`
- Posts table: title, category, status, views
- Add / Edit / Delete
- Comment moderation sub-section

---

### ADMIN PAGE 20: Add/Edit Blog Post — `/admin/blog/add` | `/admin/blog/edit/{id}`
- Title, slug, category, tags
- TinyMCE body editor
- Cover image upload
- Excerpt field
- Publish / Schedule toggle
- SEO fields

---

### ADMIN PAGE 21: Gallery — `/admin/gallery`
- Albums list with cover
- Create / Edit / Delete albums
- Per-album: drag-and-drop bulk photo upload (AJAX)
- Reorder photos
- Set cover photo
- Delete photos

---

### ADMIN PAGE 22: Announcements — `/admin/announcements`
- Table with active/expired status indicator
- Add / Edit / Delete
- Target audience selector
- Date range for display
- Show on website toggle

---

### ADMIN PAGE 23: Communications — `/admin/communications`
**Email Tab:**
- Recipient group: all members, by ministry, by cell group, or custom list
- Subject and rich text body (TinyMCE)
- Send test email
- Send now or schedule

**SMS Tab:**
- Recipient group selector
- Message text with 160-char counter
- Send now

**Logs Tab:**
- Table of all sent emails and SMS: type, recipients, subject, status, sent by, date

**Templates Tab:**
- Edit automated templates: Welcome Email, Birthday Greeting, Giving Receipt, Sunday Reminder, Absence Follow-up

---

### ADMIN PAGE 24: Reports & Analytics — `/admin/reports`
- Membership growth chart (monthly new members, 12 months)
- Attendance trend chart
- Giving trend chart
- Ministry distribution pie chart
- Demographics: gender, age groups, marital status
- Custom date range report builder
- Export all as PDF

---

### ADMIN PAGE 25: Contact Inbox — `/admin/contacts`
- Table: name, subject, date, read status
- View message
- Mark read/unread
- Reply via email (AJAX, logs response)
- Archive / Delete
- Unread count badge on sidebar

---

### ADMIN PAGE 26: Users & Roles — `/admin/users`
- Users table: name, email, role, last login, status
- Add / Edit / Delete users
- Role assignment
- Permissions matrix: per-role module access (checkbox grid)
- Reset password
- Activity log per user

---

### ADMIN PAGE 27: Site Settings — `/admin/settings`

**General Tab:** Church name, tagline, logo, favicon, address, phone, email, social media links

**Service Times Tab:** List of service types — name, day, time, venue (CRUD rows)

**Giving Tab:** Enable/disable online giving, gateway selection (Paystack/Flutterwave), API keys, bank account details

**Live Stream Tab:** YouTube Channel ID, stream URL, auto-detect live toggle, offline message

**Email Tab:** SMTP host, port, username, password, from name, from email, test email button

**SEO Tab:** Default meta title, meta description, Google Analytics ID, Facebook Pixel ID, OG image

**Maintenance Tab:** Toggle site offline with custom maintenance message

---

### ADMIN PAGE 28: Audit Log — `/admin/audit-log`
- Log of all admin actions: login, create, update, delete, export
- Filter by user, action type, date range
- Export log

---

## 8. MEMBER PORTAL PAGES

**Base URL:** `/portal` — requires authenticated member session

### PORTAL PAGE 1: Dashboard — `/portal`
- Welcome with name and photo
- Attendance score card (% this year)
- Total giving this year
- Upcoming registered events
- Announcements targeted to members
- Notification bell with unread count

### PORTAL PAGE 2: My Profile — `/portal/profile`
- View and edit personal info
- Change profile photo
- Change password
- Communication preferences (email/SMS opt-in)

### PORTAL PAGE 3: My Giving — `/portal/giving`
- Giving history table: date, type, amount, reference
- Filter by date and type
- Download annual statement as PDF
- Make a new donation (embedded give form)

### PORTAL PAGE 4: My Attendance — `/portal/attendance`
- Attendance history table
- Monthly attendance bar chart
- Attendance rate percentage and streak

### PORTAL PAGE 5: My Cell Group — `/portal/cellgroup`
- Cell group details: name, leader, meeting day/time/venue
- Fellow members list
- Meeting notes (if uploaded by leader)

### PORTAL PAGE 6: My Ministry — `/portal/ministry`
- Ministries enrolled in
- Ministry schedule and announcements

---

## 9. FEATURES & FUNCTIONALITY SPECIFICATION

### 9.1 Authentication
- Login with email + password
- Remember Me: 30-day cookie with hashed token stored in DB
- Password strength meter on register/reset
- Rate limiting: lock after 5 failed logins for 15 minutes (tracked in DB)
- Secure reset: emailed time-limited token (1-hour expiry), invalidated after use
- CSRF token on every POST form
- Session regeneration on login and privilege change
- Role middleware on every protected route

### 9.2 Clean URL Routing
- All requests → `public_html/index.php` via `.htaccess`
- `Router.php` parses URI, maps to Controller@method
- Named route groups: `/admin/*` requires admin middleware, `/portal/*` requires member middleware
- 404 custom handler
- Slug generation: `sanitize(title) + check uniqueness + append -2, -3 if collision`

### 9.3 AJAX Pattern
All AJAX calls use this convention:

**Request:** `POST /app/api/{resource}/{action}` with JSON body + `X-CSRF-Token` header
**Response:**
```json
{
    "success": true,
    "message": "Operation completed",
    "data": {},
    "errors": {}
}
```
The `ajax.js` helper wraps Fetch API, handles loading spinners, success toasts, and error toasts automatically so individual pages don't repeat this logic.

### 9.4 File Upload Rules
- Uploads saved to `public_html/uploads/{type}/{year}/{month}/`
- File renamed to UUID on save (e.g. `a3f8b2c1-4d92.mp3`) — original name never used
- MIME type validated server-side (whitelist only)
- File extension checked against whitelist
- PHP execution blocked in uploads folder via `.htaccess`
- Allowed types:
  - Images: jpg, jpeg, png, webp (max 5MB, auto-resized to max 1920px width using GD)
  - Audio: mp3, m4a (max 200MB)
  - Documents: pdf, docx (max 20MB)

### 9.5 Payment Gateway (Paystack)
1. User fills give form, clicks Pay
2. Frontend calls `/api/give/initiate` (AJAX POST) with amount and type
3. Backend creates a transaction record (status: pending), returns Paystack payment URL/reference
4. Frontend opens Paystack inline popup
5. On success: Paystack redirects to `/give/success?reference=xxx`
6. Backend verifies payment server-to-server via Paystack API (never trust client)
7. Updates giving record status to `success`, generates receipt PDF
8. Also handles Paystack webhook at `/api/give/webhook/paystack` for async confirmation

**Important:** All amounts stored as DECIMAL in Naira. Paystack expects kobo (multiply × 100 when sending, divide ÷ 100 when receiving).

### 9.6 Email Automation (PHPMailer)
Triggered events:
- New member registration → Welcome Email
- Birthday (daily cron or scheduled task via cPanel) → Birthday Greeting
- Event registration → Confirmation Email
- Successful giving → Receipt Email with PDF attachment
- Prayer request submission → Acknowledgment Email
- Password reset request → Reset Link Email
- Sunday service → Reminder SMS/Email (Saturday 8pm)
- Member absent 3+ Sundays → Pastoral Follow-up Email

On shared hosting, use cPanel's **Cron Jobs** feature to run a PHP script (`app/cron/send_queue.php`) every 5 minutes. The script sends up to 20 queued emails from `sms_emails_log` per run to avoid SMTP rate limits.

### 9.7 QR Code Attendance
- Each member has a unique QR code encoding their member code
- QR generated using `phpqrcode` library (PHP, no external call needed)
- Displayed on member portal profile and printable ID card
- Admin attendance page has a "Scan Mode" that activates device camera (HTML5 + jsQR.js)
- Scanning marks that member present for the currently open service record

### 9.8 Receipt PDF Generation
DomPDF generates a PDF receipt on every successful giving transaction:
- Church letterhead (logo + name)
- Receipt number, date
- Giver name, giving type
- Amount in figures and words
- "This is an official receipt from RCCG Prince and Princess Parish"
- Stored in `public_html/uploads/documents/receipts/`
- Attached to giving receipt email and available for download in member portal

### 9.9 Global Search
- Search bar in public nav and admin topbar
- AJAX: results appear in dropdown after 300ms debounce
- Searches: sermons (title, preacher, tags), events (title), blog posts (title, excerpt), ministries (name)
- Full search results page at `/search?q=term` grouped by type

### 9.10 SEO
- Every public page has a dynamic `<title>` and `<meta name="description">`
- Open Graph and Twitter Card meta tags on every page
- Sitemap at `/sitemap.xml` — auto-generated PHP script listing all sermons, events, blog posts, ministries, gallery albums
- `robots.txt` at root
- Canonical URLs to avoid duplicate content
- Image `alt` attributes populated from titles/captions

---

## 10. AJAX/API ENDPOINTS

All AJAX routes are defined in `app/routes/api.php` and handled through `index.php`. The URL pattern is `/api/{resource}/{action}`.

### Public API
| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/sermons/search` | Live sermon search |
| GET | `/api/sermons/filter` | Filter sermons by params |
| POST | `/api/sermons/view` | Increment view count |
| POST | `/api/sermons/download` | Increment download + serve file |
| POST | `/api/events/register` | Submit event registration |
| POST | `/api/prayer/submit` | Submit prayer request |
| POST | `/api/prayer/praying` | Increment "I'm praying" count |
| POST | `/api/newsletter/subscribe` | Subscribe to newsletter |
| POST | `/api/contact/send` | Submit contact form |
| POST | `/api/give/initiate` | Initiate payment |
| POST | `/api/give/verify` | Verify payment after redirect |
| POST | `/api/give/webhook/paystack` | Paystack webhook |
| GET | `/api/announcements` | Fetch active announcements |
| GET | `/api/livestream/status` | Check if stream is live |
| GET | `/api/search` | Global search |

### Admin API
| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/admin/members/search` | Live member search for attendance |
| POST | `/api/admin/attendance/mark` | Mark member present/absent |
| POST | `/api/admin/attendance/qr` | QR scan check-in |
| POST | `/api/admin/giving/record` | Record manual giving entry |
| GET | `/api/admin/dashboard/stats` | Refresh dashboard stats |
| POST | `/api/admin/communications/send` | Send email/SMS blast |
| POST | `/api/admin/members/import` | Import members from Excel |
| POST | `/api/admin/gallery/upload` | Bulk photo upload |
| POST | `/api/admin/sermons/upload` | Audio upload with progress |
| POST | `/api/admin/contact/reply` | Reply to contact message |
| GET | `/api/admin/notifications` | Fetch admin notifications |
| POST | `/api/admin/notifications/read` | Mark notifications read |

---

## 11. URL ROUTING MAP

### Public Routes
```
GET  /                          → Home
GET  /about                     → About
GET  /sermons                   → Sermons list
GET  /sermons/{slug}            → Single sermon
GET  /sermons/series/{slug}     → Sermon series
GET  /events                    → Events list
GET  /events/{slug}             → Single event
GET  /ministries                → Ministries list
GET  /ministries/{slug}         → Single ministry
GET  /give                      → Give page
GET  /give/success              → Giving success
GET  /prayer                    → Prayer requests
GET  /blog                      → Blog list
GET  /blog/{slug}               → Single post
GET  /gallery                   → Gallery albums
GET  /gallery/{slug}            → Gallery album
GET  /contact                   → Contact
GET  /livestream                → Live stream
GET  /join                      → Membership application
GET  /search                    → Search results
GET  /login                     → Login
GET  /register                  → Register
GET  /forgot-password           → Forgot password
GET  /reset-password/{token}    → Reset password
GET  /logout                    → Logout
GET  /sitemap.xml               → Sitemap
```

### Admin Routes
```
GET  /admin                             → Dashboard
GET  /admin/members                     → Members list
GET  /admin/members/add                 → Add member
GET  /admin/members/edit/{id}           → Edit member
GET  /admin/members/view/{id}           → View member
GET  /admin/sermons                     → Sermons list
GET  /admin/sermons/add                 → Add sermon
GET  /admin/sermons/edit/{id}           → Edit sermon
GET  /admin/sermons/series              → Sermon series
GET  /admin/events                      → Events list
GET  /admin/events/add                  → Add event
GET  /admin/events/edit/{id}            → Edit event
GET  /admin/events/{id}/registrations   → Event registrations
GET  /admin/ministries                  → Ministries
GET  /admin/cellgroups                  → Cell groups
GET  /admin/attendance                  → Mark attendance
GET  /admin/attendance/reports          → Attendance reports
GET  /admin/giving                      → Giving transactions
GET  /admin/giving/reports              → Giving reports
GET  /admin/prayer                      → Prayer requests
GET  /admin/blog                        → Blog posts
GET  /admin/blog/add                    → Add post
GET  /admin/blog/edit/{id}              → Edit post
GET  /admin/gallery                     → Gallery
GET  /admin/announcements               → Announcements
GET  /admin/communications              → Communications
GET  /admin/reports                     → Reports & analytics
GET  /admin/contacts                    → Contact inbox
GET  /admin/users                       → Users & roles
GET  /admin/settings                    → Site settings
GET  /admin/audit-log                   → Audit log
```

### Member Portal Routes
```
GET  /portal                    → Member dashboard
GET  /portal/profile            → My profile
GET  /portal/giving             → My giving
GET  /portal/attendance         → My attendance
GET  /portal/cellgroup          → My cell group
GET  /portal/ministry           → My ministry
```

---

## 12. SECURITY ARCHITECTURE

| Threat | Mitigation |
|---|---|
| SQL Injection | PDO prepared statements on every query — zero raw interpolation |
| XSS | `htmlspecialchars()` on all output, CSP header |
| CSRF | Token in session, validated on every POST |
| File Upload Attacks | MIME whitelist, extension whitelist, UUID rename, PHP execution blocked in uploads via .htaccess |
| PHP Execution in App Folder | `app/.htaccess` denies all HTTP access |
| Brute Force Login | 5 attempts → 15-min lockout recorded in DB |
| Session Hijacking | Session regeneration on login, HTTPOnly + Secure + SameSite=Strict cookies |
| Direct URL Access to Controllers | All code in `app/` which is denied by `.htaccess` |
| Payment Tampering | Server-to-server verification with Paystack before marking success |
| Sensitive Config Exposure | Credentials in `app/Config/config.php` blocked by `app/.htaccess` |
| Directory Listing | `Options -Indexes` in root `.htaccess` |
| Clickjacking | `X-Frame-Options: DENY` header |
| Insecure Uploads | Separate `.htaccess` in uploads blocks PHP execution |

---

## 13. UI/UX DESIGN SYSTEM

### Brand Colors (RCCG)
| Name | Hex | Usage |
|---|---|---|
| RCCG Red | `#C41E3A` | Primary buttons, CTAs, nav active state |
| Deep Crimson | `#8B0000` | Hover states, footer, dark sections |
| RCCG Gold | `#D4AF37` | Highlights, badges, callouts |
| Deep Navy | `#0A1628` | Headings, sidebar backgrounds |
| White | `#FFFFFF` | Card backgrounds, light text |
| Light Gray | `#F8F9FA` | Page background, alternate sections |
| Medium Gray | `#6B7280` | Body text, secondary text |
| Success Green | `#16A34A` | Success states |
| Warning Amber | `#D97706` | Warning states |
| Error Red | `#DC2626` | Error/validation states |

### Typography
- **Display/Headings:** Playfair Display (Google Fonts)
- **Body:** Inter (Google Fonts)
- Load in layouts: `<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">`

### Tailwind Custom Extension (`assets/css/custom.css`)
```css
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600&display=swap');

:root {
    --rccg-red: #C41E3A;
    --rccg-crimson: #8B0000;
    --rccg-gold: #D4AF37;
    --rccg-navy: #0A1628;
}

.font-display { font-family: 'Playfair Display', serif; }
.font-body    { font-family: 'Inter', sans-serif; }
.bg-rccg-red  { background-color: #C41E3A; }
.bg-rccg-crimson { background-color: #8B0000; }
.bg-rccg-gold { background-color: #D4AF37; }
.bg-rccg-navy { background-color: #0A1628; }
.text-rccg-red  { color: #C41E3A; }
.text-rccg-gold { color: #D4AF37; }
.border-rccg-red { border-color: #C41E3A; }
```

### Component Classes (Tailwind utility composites)
- **Primary Button:** `bg-rccg-red text-white hover:bg-rccg-crimson rounded-lg px-6 py-3 font-semibold transition-colors duration-200`
- **Gold Button:** `bg-rccg-gold text-rccg-navy hover:opacity-90 rounded-lg px-6 py-3 font-bold`
- **Outline Button:** `border-2 border-rccg-red text-rccg-red hover:bg-rccg-red hover:text-white rounded-lg px-6 py-3 font-semibold transition-colors duration-200`
- **Card:** `bg-white rounded-xl shadow-sm border border-gray-100 p-6`
- **Section Padding:** `py-16 md:py-24`
- **Container:** `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`

---

## 14. THIRD-PARTY INTEGRATIONS

| Service | Purpose | Notes |
|---|---|---|
| Paystack | Online giving payment processing | Nigerian Naira, widely used |
| Google Maps Embed API | Church location map | Free embed via iframe |
| YouTube Embed | Sermon videos + live stream | No API key needed for embed |
| Africa's Talking / Termii | Bulk SMS | Nigeria-focused SMS APIs |
| Google Fonts | Playfair Display + Inter | CDN in layout head |
| TinyMCE | Rich text editor for blog/sermons | CDN version (free tier) |
| Chart.js | Dashboard charts | CDN |
| FullCalendar.js | Events calendar | CDN |
| Plyr.js | Sermon audio/video player | CDN |
| DataTables.js | Admin data tables | CDN |
| GLightbox | Gallery photo lightbox | CDN |
| Flatpickr | Date pickers in forms | CDN |
| SweetAlert2 | Confirmation dialogs | CDN |
| Croppie.js | Profile photo cropping | CDN |
| jsQR.js | QR code scanning | CDN |
| phpqrcode | QR code generation (PHP) | Included in vendor/ |
| DomPDF | PDF receipts and reports | Composer package |
| PhpSpreadsheet | Excel import/export | Composer package |
| PHPMailer | Transactional emails | Composer package |
| Google reCAPTCHA v3 | Spam protection on public forms | API key in settings |
| Google Analytics 4 | Traffic analytics | GA4 tag via settings |

---

## 15. DEVELOPMENT PHASES & MILESTONES

### Phase 1 — Foundation (Weeks 1–2)
- [ ] Shared hosting setup, domain, SSL certificate
- [ ] cPanel database creation, user, permissions
- [ ] Folder structure setup, .htaccess files in place
- [ ] Routing engine, base Controller, base Model, PDO class
- [ ] Tailwind + custom CSS baseline
- [ ] Public layout (header, footer, nav) with RCCG branding
- [ ] Admin layout (sidebar, topbar)
- [ ] Member portal layout
- [ ] Authentication: login, logout, session, password reset
- [ ] Role middleware
- [ ] All database migrations (run all SQL files)
- [ ] Site settings loader (used everywhere)

### Phase 2 — Public Website (Weeks 3–5)
- [ ] Home page (all sections, AJAX announcements ticker)
- [ ] About page
- [ ] Sermons: list, single (Plyr player), series
- [ ] Events: list (FullCalendar), single (registration form)
- [ ] Ministries: list, single (join form)
- [ ] Gallery: albums, album lightbox
- [ ] Blog: list, single
- [ ] Contact page (AJAX + reCAPTCHA)
- [ ] Prayer request page (AJAX form, prayer wall)
- [ ] Search results page
- [ ] 404 page
- [ ] Membership application multi-step form

### Phase 3 — Giving & Payments (Week 6)
- [ ] Give page UI with tabs and amounts
- [ ] Paystack integration (initiate, callback page, webhook)
- [ ] Server-to-server payment verification
- [ ] DomPDF receipt generation
- [ ] Receipt email (PHPMailer)
- [ ] Giving success page
- [ ] Bank transfer manual flow

### Phase 4 — Admin CMS Core (Weeks 7–9)
- [ ] Dashboard with Chart.js (attendance + giving charts)
- [ ] Member management: list (DataTables), add, edit, view
- [ ] Member photo upload with Croppie.js
- [ ] Excel member import (PhpSpreadsheet)
- [ ] Member export (CSV, Excel, PDF)
- [ ] Sermon management: list, add/edit (TinyMCE + audio upload with progress)
- [ ] Sermon series management
- [ ] Event management: list, add/edit, registrations view
- [ ] Blog management: list, add/edit (TinyMCE)
- [ ] Gallery management: albums, bulk upload
- [ ] Announcements management

### Phase 5 — Church Management System (Weeks 10–11)
- [ ] Attendance: mark page (AJAX member search + checkboxes)
- [ ] Attendance: QR scanner mode (jsQR.js)
- [ ] Attendance: close service, visitor counts
- [ ] Attendance reports (charts, per-member %, export)
- [ ] Giving records: manual entry, transaction table
- [ ] Giving reports: charts, per-member statements, PDF
- [ ] Prayer request management: assign, status update, reply
- [ ] Cell group management (CRUD, member assignment)
- [ ] Ministry management (CRUD, member management)

### Phase 6 — Communications & Member Portal (Week 12)
- [ ] Email blast composer (PHPMailer, recipient groups)
- [ ] SMS blast (Africa's Talking/Termii API)
- [ ] cPanel Cron job: email/SMS queue sender
- [ ] Automated triggers: birthday, welcome, receipt, reminder
- [ ] Email template editor
- [ ] Member portal: dashboard, profile editor, giving history
- [ ] Member attendance history chart
- [ ] Member cell group view
- [ ] Member portal giving PDF statement

### Phase 7 — Polish & Advanced (Week 13)
- [ ] Live stream page (YouTube embed + countdown)
- [ ] Sitemap.xml dynamic generator
- [ ] SEO meta tags on all pages
- [ ] OG tags (Facebook share preview)
- [ ] User management + permissions matrix
- [ ] Audit log (log all admin actions)
- [ ] Contact inbox with reply system
- [ ] Site settings: all tabs functional
- [ ] Admin notifications system

### Phase 8 — Testing & Launch (Weeks 14–15)
- [ ] Cross-browser testing (Chrome, Firefox, Safari, Edge)
- [ ] Mobile responsiveness audit (all pages on real devices)
- [ ] Form validation edge cases (all forms)
- [ ] Payment testing: Paystack test mode, then live mode
- [ ] Email delivery testing (all automated emails)
- [ ] SMS delivery test
- [ ] Security checklist: CSRF, SQL injection pen test, upload bypass test
- [ ] Performance: enable gzip in .htaccess, image optimization
- [ ] Create first super_admin account
- [ ] Seed initial data: service times, ministries, first admin user
- [ ] DNS configuration and go-live

---

## APPENDIX A: NAMING CONVENTIONS

| Type | Convention | Example |
|---|---|---|
| PHP classes | PascalCase | `MemberModel`, `AttendanceController` |
| PHP methods | camelCase | `getAllActiveMembers()` |
| PHP variables | camelCase | `$memberCount`, `$givingTotal` |
| Database tables | snake_case plural | `sermon_series`, `cell_groups` |
| Database columns | snake_case | `first_name`, `created_at` |
| URL paths | kebab-case | `/admin/cell-groups` |
| PHP view files | kebab-case | `single-sermon.php` |
| JS functions | camelCase | `submitPrayerRequest()` |
| CSS custom classes | kebab-case | `bg-rccg-red`, `font-display` |
| Upload files | UUID format | `a3f8b2c1-4d92-11ee.mp3` |

## APPENDIX B: cPanel CRON JOB

To process the email/SMS queue (runs every 5 minutes):
```
*/5 * * * * /usr/local/bin/php /home/{cpanelusername}/public_html/app/cron/send_queue.php
```

Birthday greetings (runs daily at 8am):
```
0 8 * * * /usr/local/bin/php /home/{cpanelusername}/public_html/app/cron/birthday_greetings.php
```

Absence follow-up check (runs every Monday at 9am):
```
0 9 * * 1 /usr/local/bin/php /home/{cpanelusername}/public_html