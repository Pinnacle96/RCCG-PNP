<?php
/**
 * Email queue helper.
 *
 * Producers (receipt emails, welcome emails, automation jobs) enqueue rows into
 * `sms_emails_log` with status='pending'. The CLI worker app/cron/send_queue.php
 * consumes them and dispatches via Core/Mailer. This keeps user-facing requests
 * fast and lets delivery be retried out of band (SSOT §7).
 */
class Queue {
    /** Enqueue an HTML email for later delivery by the cron worker. */
    public static function email(string $to, string $subject, string $body, ?int $sentBy = null): void {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        Database::insert('sms_emails_log', [
            'type' => 'email',
            'recipient' => $to,
            'subject' => $subject,
            'body' => $body,
            'status' => 'pending',
            'sent_by' => $sentBy,
        ]);
    }

    /**
     * Process up to $limit pending email rows. Returns [sent, failed].
     * Safe to call repeatedly (from cron); marks each row sent/failed.
     */
    public static function process(int $limit = 50): array {
        $rows = Database::fetchAll(
            "SELECT id, recipient, subject, body FROM sms_emails_log
             WHERE type = 'email' AND status = 'pending'
             ORDER BY id ASC LIMIT " . (int) $limit
        );

        if (empty($rows)) {
            return [0, 0];
        }

        $mailer = new Mailer();
        $sent = 0;
        $failed = 0;
        foreach ($rows as $row) {
            $ok = $mailer->send($row['recipient'], (string) $row['subject'], (string) $row['body'], true);
            Database::update('sms_emails_log', [
                'status' => $ok ? 'sent' : 'failed',
            ], 'id = :id', [':id' => $row['id']]);
            $ok ? $sent++ : $failed++;
        }

        return [$sent, $failed];
    }
}
