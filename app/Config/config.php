<?php
/**
 * Application Configuration
 * RCCG Prince and Princess Parish
 *
 * SSOT §4.4: Store credentials in this file — never in a .env file.
 * This file is blocked from direct HTTP access by app/.htaccess (Deny from all).
 */

// ===========================
// Local Overrides (secrets, environment)
// ===========================
// SSOT §4.4: credentials live in PHP config, never a .env file. To keep real
// secrets out of version control, an optional config.local.php (gitignored)
// may define any constant below FIRST — defined() guards let it win.
if (is_file(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// ===========================
// Environment Detection
// ===========================
// Auto-detect environment from the request host so a single committed config
// is safe in both local dev and production. config.local.php may predefine
// ENVIRONMENT to override.
if (!defined('ENVIRONMENT')) {
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
    $host = strtolower((string) preg_replace('/:\d+$/', '', $host));
    $isLocal = in_array($host, ['localhost', '127.0.0.1', '::1', ''], true)
        || str_ends_with($host, '.local')
        || str_ends_with($host, '.test');
    // CLI tools (cron/console) are treated as development for verbose output.
    define('ENVIRONMENT', ($isLocal || PHP_SAPI === 'cli') ? 'development' : 'production');
}

// True when the current request is served over HTTPS (for secure cookies/HSTS).
if (!function_exists('isHttps')) {
    function isHttps(): bool {
        if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
            return true;
        }
        if (($_SERVER['SERVER_PORT'] ?? null) == 443) {
            return true;
        }
        return strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
    }
}

// ===========================
// Error Reporting
// ===========================
if (!defined('DEBUG')) {
    define('DEBUG', ENVIRONMENT === 'development'); // verbose only outside production
}
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}
ini_set('error_log', LOG_PATH . '/php-errors.log');

// ===========================
// Database Configuration
// ===========================
// Guarded with defined() so config.local.php can supply production credentials.
defined('DB_HOST') || define('DB_HOST', 'localhost');
defined('DB_NAME') || define('DB_NAME', 'rccgpp_db');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');
defined('DB_CHARSET') || define('DB_CHARSET', 'utf8mb4');

// ===========================
// Site Configuration
// ===========================
define('SITE_NAME', 'RCCG Prince and Princess Parish');
define('SITE_TAGLINE', 'Growing in Faith • Serving with Love');
defined('BASE_URL') || define('BASE_URL', 'http://localhost/rccgpp');
define('APP_URL', BASE_URL);
define('APP_PATH', APP . '/');
define('UPLOAD_PATH', ROOT . '/uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');

// ===========================
// Paths
// ===========================
define('VIEW_PATH', APP . '/Views/');
define('LAYOUT_PATH', VIEW_PATH . 'layouts/');
define('PARTIAL_PATH', LAYOUT_PATH . 'partials/');
define('ROUTE_PATH', APP . '/routes/');
define('MIGRATION_PATH', APP . '/migrations/');

// ===========================
// Session & Security
// ===========================
define('SESSION_TIMEOUT', 86400); // 24 hours in seconds
define('PASSWORD_RESET_EXPIRY', 3600); // 1 hour
define('REMEMBER_ME_DAYS', 30);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_MINUTES', 15);
define('CSRF_TOKEN_NAME', '_csrf_token');
define('CSRF_HEADER_NAME', 'X-CSRF-Token');

// Session Configuration
session_name('rccgpp_session');

// Set session save path to writable directory
$sessionPath = STORAGE . '/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);

session_set_cookie_params([
    'lifetime' => SESSION_TIMEOUT,
    'path' => '/',
    'domain' => '',
    'secure' => isHttps(), // automatically true under HTTPS (production)
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===========================
// Pagination
// ===========================
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 25);

// ===========================
// File Upload Limits
// ===========================
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_AUDIO_SIZE', 200 * 1024 * 1024); // 200MB
define('MAX_DOC_SIZE', 20 * 1024 * 1024); // 20MB
define('MAX_IMAGE_WIDTH', 1920); // Resize images to max 1920px width

// Allowed file types
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_AUDIO_TYPES', ['mp3', 'm4a']);
define('ALLOWED_DOC_TYPES', ['pdf', 'docx']);

// ===========================
// Currency
// ===========================
define('CURRENCY', 'NGN');
define('CURRENCY_SYMBOL', '₦');

// ===========================
// Roles
// ===========================
define('ROLE_SUPER_ADMIN', 'super_admin');
define('ROLE_ADMIN', 'admin');
define('ROLE_PASTOR', 'pastor');
define('ROLE_DEACON', 'deacon');
define('ROLE_CELL_LEADER', 'cell_leader');
define('ROLE_MEMBER', 'member');

// ===========================
// Timezone
// ===========================
date_default_timezone_set('Africa/Lagos');

// ===========================
// Email Defaults
// ===========================
define('MAIL_FROM_NAME', SITE_NAME);
define('MAIL_FROM_EMAIL', 'noreply@rccgprinceandprincess.org');
define('MAIL_REPLY_TO', 'info@rccgprinceandprincess.org');

// ===========================
// Payment Gateway (Paystack)
// ===========================
defined('PAYSTACK_PUBLIC_KEY') || define('PAYSTACK_PUBLIC_KEY', 'pk_test_your_paystack_public_key_here');
defined('PAYSTACK_SECRET_KEY') || define('PAYSTACK_SECRET_KEY', 'sk_test_your_paystack_secret_key_here');
define('PAYSTACK_BASE_URL', 'https://api.paystack.co');

// ===========================
// SMS Gateway (Africa's Talking)
// ===========================
defined('SMS_API_KEY') || define('SMS_API_KEY', 'your_at_api_key_here');
defined('SMS_USERNAME') || define('SMS_USERNAME', 'sandbox'); // Change to production username

// ===========================
// reCAPTCHA v3
// ===========================
defined('RECAPTCHA_SITE_KEY') || define('RECAPTCHA_SITE_KEY', 'your_recaptcha_site_key_here');
defined('RECAPTCHA_SECRET_KEY') || define('RECAPTCHA_SECRET_KEY', 'your_recaptcha_secret_key_here');
// Minimum reCAPTCHA v3 score (0.0–1.0) to accept a submission.
defined('RECAPTCHA_MIN_SCORE') || define('RECAPTCHA_MIN_SCORE', 0.5);

// ===========================
// Service Times (defaults — overridable in settings)
// ===========================
define('SERVICE_SUNDAY_FIRST', 'Sunday Worship Service - 08:00 AM');
define('SERVICE_SUNDAY_SECOND', '');
define('SERVICE_WEDNESDAY', 'Wednesday Winning Way - 6:00 PM');
define('SERVICE_FRIDAY', 'Friday Prayer Night - 10:00 PM');

// ===========================
// Church Contact Info (defaults — overridable in settings)
// ===========================
define('CHURCH_ADDRESS', 'Chevron Area, Esa-Oke');
define('CHURCH_PHONE', '08033852846');
define('CHURCH_EMAIL', 'info@rccgprinceandprincess.org');
define('CHURCH_LATITUDE', '6.5244');
define('CHURCH_LONGITUDE', '3.3792');
