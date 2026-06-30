<?php
/**
 * Email queue worker (SSOT §9.6, Appendix B).
 * cPanel cron: every 5 minutes
 *   /usr/local/bin/php /home/{user}/public_html/app/cron/send_queue.php
 */

require_once dirname(__DIR__) . '/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script must be run from the command line.');
}

[$sent, $failed] = \Queue::process(20);
fwrite(STDOUT, sprintf('[%s] Queue processed: %d sent, %d failed%s', date('Y-m-d H:i:s'), $sent, $failed, PHP_EOL));
