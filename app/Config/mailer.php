<?php
/**
 * Mailer Configuration
 * PHPMailer SMTP settings
 */

return [
    'driver'      => 'smtp', // smtp | mail
    'host'        => 'smtp.gmail.com',
    'port'        => 587,
    'username'    => '',
    'password'    => '',
    'encryption'  => 'tls', // tls | ssl
    'from_name'   => MAIL_FROM_NAME,
    'from_email'  => MAIL_FROM_EMAIL,
    'reply_to'    => MAIL_REPLY_TO,
];
