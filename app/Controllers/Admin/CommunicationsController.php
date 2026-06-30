<?php

namespace App\Controllers\Admin;

class CommunicationsController extends \Controller {
    private const MANAGE = [ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PASTOR];

    /** GET /admin/communications */
    public function index(): void {
        $this->requireRole(self::MANAGE);

        $logs = \Database::fetchAll(
            "SELECT type, recipient, subject, status, sent_at
             FROM sms_emails_log
             ORDER BY sent_at DESC, id DESC
             LIMIT 100"
        );

        $this->view('admin.communications.index', [
            'title' => 'Communications',
            'page_title' => 'Communications',
            'ministries' => \Database::fetchAll('SELECT id AS value, name AS label FROM ministries WHERE is_active = 1 ORDER BY name'),
            'cellGroups' => \Database::fetchAll('SELECT id AS value, name AS label FROM cell_groups WHERE is_active = 1 ORDER BY name'),
            'logs' => $logs,
        ], 'admin');
    }

    /** POST /admin/communications/send */
    public function send(): void {
        $this->requireRole(self::MANAGE);
        $this->verifyCsrf();

        $group = (string) $this->input('recipient_group', 'all');
        $subject = trim((string) $this->input('subject', ''));
        $body = (string) $this->input('body', '');

        if ($subject === '' || trim(strip_tags($body)) === '') {
            $this->setFlash('error', 'Subject and message body are required.');
            $this->redirect(BASE_URL . '/admin/communications');
        }

        $recipients = $this->resolveRecipients($group);
        if (empty($recipients)) {
            $this->setFlash('error', 'No recipients with an email address were found for that group.');
            $this->redirect(BASE_URL . '/admin/communications');
        }

        $mailer = new \Mailer();
        $sent = 0;
        $failed = 0;
        foreach ($recipients as $email) {
            $ok = $mailer->send($email, $subject, $body, true);
            $ok ? $sent++ : $failed++;
            \Database::insert('sms_emails_log', [
                'type' => 'email',
                'recipient' => $email,
                'subject' => $subject,
                'body' => $body,
                'status' => $ok ? 'sent' : 'failed',
                'sent_by' => $this->userId,
            ]);
        }

        \Audit::log('send', 'communications', sprintf('Email blast "%s" to %s — %d sent, %d failed', $subject, $group, $sent, $failed));
        $this->setFlash($failed === 0 ? 'success' : 'error', sprintf('Email blast complete: %d sent, %d failed.', $sent, $failed));
        $this->redirect(BASE_URL . '/admin/communications');
    }

    /** POST /admin/communications/sms */
    public function sendSms(): void {
        $this->requireRole(self::MANAGE);
        $this->verifyCsrf();

        $group = (string) $this->input('recipient_group', 'all');
        $message = trim((string) $this->input('message', ''));

        if ($message === '') {
            $this->setFlash('error', 'Message text is required.');
            $this->redirect(BASE_URL . '/admin/communications');
        }

        $recipients = $this->resolvePhones($group);
        if (empty($recipients)) {
            $this->setFlash('error', 'No recipients with a phone number were found for that group.');
            $this->redirect(BASE_URL . '/admin/communications');
        }

        $sent = 0;
        $failed = 0;
        foreach ($recipients as $phone) {
            $ok = \Sms::send($phone, $message);
            $ok ? $sent++ : $failed++;
            \Database::insert('sms_emails_log', [
                'type' => 'sms',
                'recipient' => $phone,
                'subject' => null,
                'body' => $message,
                'status' => $ok ? 'sent' : 'failed',
                'sent_by' => $this->userId,
            ]);
        }

        \Audit::log('send', 'communications', sprintf('SMS blast to %s — %d sent, %d failed', $group, $sent, $failed));
        if (!\Sms::configured()) {
            $this->setFlash('error', 'SMS gateway is not configured (set SMS_API_KEY). ' . count($recipients) . ' message(s) were logged as failed.');
        } else {
            $this->setFlash($failed === 0 ? 'success' : 'error', sprintf('SMS blast complete: %d sent, %d failed.', $sent, $failed));
        }
        $this->redirect(BASE_URL . '/admin/communications');
    }

    /**
     * Resolve a list of unique member email addresses for a recipient group.
     */
    private function resolveRecipients(string $group): array {
        if ($group === 'ministry') {
            $id = (int) $this->input('ministry_id', 0);
            $rows = \Database::fetchAll(
                "SELECT DISTINCT m.email
                 FROM members m
                 INNER JOIN ministry_members mm ON mm.member_id = m.id AND mm.is_active = 1
                 WHERE mm.ministry_id = ? AND m.email IS NOT NULL AND m.email != ''",
                [$id]
            );
        } elseif ($group === 'cellgroup') {
            $id = (int) $this->input('cell_group_id', 0);
            $rows = \Database::fetchAll(
                "SELECT DISTINCT email FROM members
                 WHERE cell_group_id = ? AND email IS NOT NULL AND email != ''",
                [$id]
            );
        } else {
            $rows = \Database::fetchAll(
                "SELECT DISTINCT email FROM members
                 WHERE membership_status = 'active' AND email IS NOT NULL AND email != ''"
            );
        }

        $emails = [];
        foreach ($rows as $row) {
            $email = trim((string) $row['email']);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[strtolower($email)] = $email;
            }
        }
        return array_values($emails);
    }

    /** Resolve a list of unique member phone numbers for a recipient group. */
    private function resolvePhones(string $group): array {
        if ($group === 'ministry') {
            $id = (int) $this->input('ministry_id', 0);
            $rows = \Database::fetchAll(
                "SELECT DISTINCT m.phone
                 FROM members m
                 INNER JOIN ministry_members mm ON mm.member_id = m.id AND mm.is_active = 1
                 WHERE mm.ministry_id = ? AND m.phone IS NOT NULL AND m.phone != ''",
                [$id]
            );
        } elseif ($group === 'cellgroup') {
            $id = (int) $this->input('cell_group_id', 0);
            $rows = \Database::fetchAll(
                "SELECT DISTINCT phone FROM members
                 WHERE cell_group_id = ? AND phone IS NOT NULL AND phone != ''",
                [$id]
            );
        } else {
            $rows = \Database::fetchAll(
                "SELECT DISTINCT phone FROM members
                 WHERE membership_status = 'active' AND phone IS NOT NULL AND phone != ''"
            );
        }

        $phones = [];
        foreach ($rows as $row) {
            $phone = trim((string) $row['phone']);
            if ($phone !== '') {
                $phones[$phone] = $phone;
            }
        }
        return array_values($phones);
    }
}
