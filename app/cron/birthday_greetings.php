<?php
/**
 * Birthday greeting enqueuer (SSOT §9.6, Appendix B).
 * cPanel cron: daily at 8am
 *   0 8 * * *  /usr/local/bin/php /home/{user}/public_html/app/cron/birthday_greetings.php
 */

require_once dirname(__DIR__) . '/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script must be run from the command line.');
}

$siteName = \Settings::get('site_name', SITE_NAME);
$members = \Database::fetchAll(
    "SELECT first_name, email FROM members
     WHERE membership_status = 'active'
       AND email IS NOT NULL AND email != ''
       AND date_of_birth IS NOT NULL
       AND DATE_FORMAT(date_of_birth, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')"
);

$count = 0;
foreach ($members as $m) {
    $body = '<p>Dear ' . \Helpers::escape($m['first_name']) . ',</p>'
        . '<p>Happy birthday! The entire family of ' . \Helpers::escape($siteName)
        . ' celebrates God&rsquo;s gift of life in you today. May this new year be filled with grace, favour and joy.</p>'
        . '<p>With love,<br>' . \Helpers::escape($siteName) . '</p>';
    \Queue::email($m['email'], 'Happy Birthday from ' . $siteName . '!', $body);
    $count++;
}

fwrite(STDOUT, sprintf('[%s] Birthday greetings queued: %d%s', date('Y-m-d H:i:s'), $count, PHP_EOL));
