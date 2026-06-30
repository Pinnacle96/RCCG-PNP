<?php
/**
 * Absence follow-up enqueuer (SSOT §9.6, Appendix B).
 * Emails active members who missed the most recent 3 services but have
 * attended at least once before (so brand-new contacts are not pestered).
 * cPanel cron: Mondays at 9am
 *   0 9 * * 1  /usr/local/bin/php /home/{user}/public_html/app/cron/absence_followup.php
 */

require_once dirname(__DIR__) . '/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script must be run from the command line.');
}

$siteName = \Settings::get('site_name', SITE_NAME);

$serviceIds = array_column(
    \Database::fetchAll('SELECT id FROM services ORDER BY service_date DESC LIMIT 3'),
    'id'
);

if (count($serviceIds) < 3) {
    fwrite(STDOUT, sprintf('[%s] Absence follow-up skipped: fewer than 3 services on record%s', date('Y-m-d H:i:s'), PHP_EOL));
    return;
}

$placeholders = implode(',', array_fill(0, count($serviceIds), '?'));
$members = \Database::fetchAll(
    "SELECT m.first_name, m.email FROM members m
     WHERE m.membership_status = 'active'
       AND m.email IS NOT NULL AND m.email != ''
       AND EXISTS (SELECT 1 FROM attendance a2 WHERE a2.member_id = m.id)
       AND NOT EXISTS (SELECT 1 FROM attendance a WHERE a.member_id = m.id AND a.service_id IN ($placeholders))",
    $serviceIds
);

$count = 0;
foreach ($members as $m) {
    $body = '<p>Dear ' . \Helpers::escape($m['first_name']) . ',</p>'
        . '<p>We have missed you at our recent services and wanted you to know you are loved and remembered. '
        . 'We look forward to worshipping with you again soon.</p>'
        . '<p>Warm regards,<br>' . \Helpers::escape($siteName) . '</p>';
    \Queue::email($m['email'], 'We miss you at ' . $siteName, $body);
    $count++;
}

fwrite(STDOUT, sprintf('[%s] Absence follow-ups queued: %d%s', date('Y-m-d H:i:s'), $count, PHP_EOL));
