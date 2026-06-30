<?php
/**
 * Local / Production Configuration Overrides — EXAMPLE
 *
 * Copy this file to `config.local.php` in the same directory and fill in the
 * real values for your environment. `config.local.php` is gitignored, so real
 * secrets never enter version control (SSOT §4.4 keeps credentials in PHP, not
 * a .env file).
 *
 * config.php loads this file FIRST and every overridable constant is guarded
 * with defined(), so anything you define here wins over the committed defaults.
 *
 * Define ONLY what differs from the defaults — you do not need every key.
 */

// --- Environment ---------------------------------------------------------
// Force the environment instead of auto-detecting from the host.
// Controls DEBUG (false in production) and other safe defaults.
// define('ENVIRONMENT', 'production');

// --- Database ------------------------------------------------------------
// define('DB_HOST', 'localhost');
// define('DB_NAME', 'cpanelusername_rccgdb');
// define('DB_USER', 'cpanelusername_dbuser');
// define('DB_PASS', 'StrongPasswordHere');

// --- Site ----------------------------------------------------------------
// define('BASE_URL', 'https://www.rccgprinceandprincess.org');

// --- Paystack ------------------------------------------------------------
// define('PAYSTACK_PUBLIC_KEY', 'pk_live_xxxxxxxxxxxxxxxx');
// define('PAYSTACK_SECRET_KEY', 'sk_live_xxxxxxxxxxxxxxxx');

// --- SMS (Africa's Talking) ----------------------------------------------
// define('SMS_API_KEY', 'your_real_api_key');
// define('SMS_USERNAME', 'your_production_username');

// --- reCAPTCHA v3 --------------------------------------------------------
// define('RECAPTCHA_SITE_KEY', '6Lxxxxxxxxxxxxxxxxxx');
// define('RECAPTCHA_SECRET_KEY', '6Lxxxxxxxxxxxxxxxxxx');
// define('RECAPTCHA_MIN_SCORE', 0.5);
