-- RCCG Prince and Princess Parish - Database Schema
-- All tables use InnoDB engine with utf8mb4 charset

-- Table: users
CREATE TABLE IF NOT EXISTS users (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: members
CREATE TABLE IF NOT EXISTS members (
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
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_member_code (member_code),
    INDEX idx_cell_group (cell_group_id),
    INDEX idx_ministry (ministry_id),
    INDEX idx_status (membership_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sermons
CREATE TABLE IF NOT EXISTS sermons (
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
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_date (sermon_date),
    INDEX idx_published (is_published),
    INDEX idx_series (series_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sermon_series
CREATE TABLE IF NOT EXISTS sermon_series (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    slug        VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    cover_image VARCHAR(255) NULL,
    start_date  DATE NULL,
    end_date    DATE NULL,
    is_active   TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: events
CREATE TABLE IF NOT EXISTS events (
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
    updated_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_date (event_date),
    INDEX idx_published (is_published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: event_registrations
CREATE TABLE IF NOT EXISTS event_registrations (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id    INT UNSIGNED NOT NULL,
    member_id   INT UNSIGNED NULL,
    name        VARCHAR(160) NOT NULL,
    email       VARCHAR(150) NULL,
    phone       VARCHAR(20) NULL,
    adults      INT DEFAULT 1,
    children    INT DEFAULT 0,
    status      ENUM('pending','confirmed','cancelled') DEFAULT 'confirmed',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event (event_id),
    INDEX idx_member (member_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ministries
CREATE TABLE IF NOT EXISTS ministries (
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
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ministry_members
CREATE TABLE IF NOT EXISTS ministry_members (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ministry_id INT UNSIGNED NOT NULL,
    member_id   INT UNSIGNED NOT NULL,
    role        VARCHAR(80) NULL,
    joined_date DATE NULL,
    is_active   TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_member_ministry (ministry_id, member_id),
    INDEX idx_ministry (ministry_id),
    INDEX idx_member (member_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: cell_groups
CREATE TABLE IF NOT EXISTS cell_groups (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(160) NOT NULL,
    zone            VARCHAR(100) NULL,
    leader_id       INT UNSIGNED NULL,
    co_leader_id    INT UNSIGNED NULL,
    meeting_day     ENUM('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NULL,
    meeting_time    TIME NULL,
    meeting_venue   VARCHAR(255) NULL,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: services
CREATE TABLE IF NOT EXISTS services (
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
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_date (service_date),
    INDEX idx_type (service_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: attendance
CREATE TABLE IF NOT EXISTS attendance (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id      INT UNSIGNED NOT NULL,
    member_id       INT UNSIGNED NOT NULL,
    check_in_time   DATETIME NULL,
    method          ENUM('manual','qr','self') DEFAULT 'manual',
    marked_by       INT UNSIGNED NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attendance (service_id, member_id),
    INDEX idx_service (service_id),
    INDEX idx_member (member_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: giving
CREATE TABLE IF NOT EXISTS giving (
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
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_reference (reference_no),
    INDEX idx_member (member_id),
    INDEX idx_date (giving_date),
    INDEX idx_status (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: prayer_requests
CREATE TABLE IF NOT EXISTS prayer_requests (
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
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_member (member_id),
    INDEX idx_status (status),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: blog_posts
CREATE TABLE IF NOT EXISTS blog_posts (
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
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_published (is_published),
    INDEX idx_author (author_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: gallery_albums
CREATE TABLE IF NOT EXISTS gallery_albums (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    slug        VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    cover_image VARCHAR(255) NULL,
    event_date  DATE NULL,
    is_published TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_published (is_published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: gallery
CREATE TABLE IF NOT EXISTS gallery (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    album_id    INT UNSIGNED NOT NULL,
    title       VARCHAR(255) NULL,
    file_path   VARCHAR(255) NOT NULL,
    file_type   ENUM('image','video') DEFAULT 'image',
    is_featured TINYINT(1) DEFAULT 0,
    sort_order  INT DEFAULT 0,
    created_by  INT UNSIGNED NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_album (album_id),
    INDEX idx_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: announcements
CREATE TABLE IF NOT EXISTS announcements (
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
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_active (is_active),
    INDEX idx_target (target),
    INDEX idx_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: contacts
CREATE TABLE IF NOT EXISTS contacts (
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
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: notifications
CREATE TABLE IF NOT EXISTS notifications (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    type        VARCHAR(80) NOT NULL,
    title       VARCHAR(255) NOT NULL,
    message     TEXT NULL,
    link        VARCHAR(500) NULL,
    is_read     TINYINT(1) DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: site_settings
CREATE TABLE IF NOT EXISTS site_settings (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_val TEXT NULL,
    group_name  VARCHAR(80) NULL,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sms_emails_log
CREATE TABLE IF NOT EXISTS sms_emails_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type        ENUM('email','sms') NOT NULL,
    recipient   VARCHAR(255) NOT NULL,
    subject     VARCHAR(255) NULL,
    body        TEXT NULL,
    status      ENUM('sent','failed','pending') DEFAULT 'pending',
    sent_by     INT UNSIGNED NULL,
    sent_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_sent_at (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: audit_log
CREATE TABLE IF NOT EXISTS audit_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NULL,
    action      VARCHAR(100) NOT NULL,
    module      VARCHAR(80) NULL,
    description TEXT NULL,
    ip_address  VARCHAR(45) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
