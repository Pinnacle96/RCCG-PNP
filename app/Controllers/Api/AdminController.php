<?php

namespace App\Controllers\Api;

/**
 * Admin AJAX endpoints (SSOT §10 — Admin API).
 * All endpoints require an authenticated staff/admin session and, for
 * state-changing calls, a valid CSRF token.
 */
class AdminController extends ApiController {

    /* ------------------------------------------------------------------ */
    /* Members                                                             */
    /* ------------------------------------------------------------------ */

    /** GET /api/admin/members/search?q= — live member search (attendance, assignment). */
    public function memberSearch(): void {
        $this->requireAdminApi();
        $q = (string) $this->param('q', '');
        $rows = [];
        if ($q !== '') {
            $term = '%' . $q . '%';
            $rows = \Database::fetchAll(
                "SELECT id, member_code, first_name, last_name, phone, email, profile_photo
                 FROM members
                 WHERE membership_status = 'active'
                   AND (member_code LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR email LIKE ?)
                 ORDER BY first_name, last_name LIMIT 15",
                [$term, $term, $term, $term, $term]
            );
        }
        $this->ok(['results' => $rows]);
    }

    /* ------------------------------------------------------------------ */
    /* Attendance                                                          */
    /* ------------------------------------------------------------------ */

    /** POST /api/admin/attendance/mark — mark a member present for a service. */
    public function attendanceMark(): void {
        $this->requireAdminApi();
        $this->requirePost();
        $this->guardCsrf();

        $serviceId = (int) $this->param('service_id', 0);
        $memberId = (int) $this->param('member_id', 0);
        $service = $this->openServiceOr404($serviceId);
        if ($memberId <= 0) {
            $this->fail('Select a member to mark present.', [], 422);
        }
        $member = \Database::fetchOne('SELECT id, first_name, last_name FROM members WHERE id = ?', [$memberId]);
        if (!$member) {
            $this->fail('Member not found.', [], 404);
        }

        (new \App\Models\AttendanceModel())->mark($serviceId, $memberId, (int) $this->userId, 'manual');
        $this->syncTotal($serviceId);
        $count = (int) \Database::fetchColumn('SELECT COUNT(*) FROM attendance WHERE service_id = ?', [$serviceId]);

        $this->ok([
            'member' => ['id' => (int) $member['id'], 'name' => $member['first_name'] . ' ' . $member['last_name']],
            'marked_count' => $count,
        ], 'Marked present.');
    }

    /** POST /api/admin/attendance/qr — QR scan check-in by member code. */
    public function attendanceQr(): void {
        $this->requireAdminApi();
        $this->requirePost();
        $this->guardCsrf();

        $serviceId = (int) $this->param('service_id', 0);
        $this->openServiceOr404($serviceId);
        $code = strtoupper(trim((string) $this->param('code', '')));
        if ($code === '') {
            $this->fail('No QR code detected.', [], 422);
        }
        $member = \Database::fetchOne('SELECT id, first_name, last_name FROM members WHERE member_code = ?', [$code]);
        if (!$member) {
            $this->fail('No member matches code ' . $code . '.', [], 404);
        }

        $already = \Database::fetchOne('SELECT id FROM attendance WHERE service_id = ? AND member_id = ?', [$serviceId, $member['id']]);
        (new \App\Models\AttendanceModel())->mark($serviceId, (int) $member['id'], (int) $this->userId, 'qr');
        $this->syncTotal($serviceId);
        $count = (int) \Database::fetchColumn('SELECT COUNT(*) FROM attendance WHERE service_id = ?', [$serviceId]);

        $this->ok([
            'member' => ['id' => (int) $member['id'], 'name' => $member['first_name'] . ' ' . $member['last_name'], 'code' => $code],
            'already' => (bool) $already,
            'marked_count' => $count,
        ], $already ? $member['first_name'] . ' was already present.' : $member['first_name'] . ' marked present.');
    }

    private function openServiceOr404(int $serviceId): array {
        $service = $serviceId > 0 ? \Database::fetchOne('SELECT * FROM services WHERE id = ?', [$serviceId]) : null;
        if (!$service) {
            $this->fail('Service not found.', [], 404);
        }
        if ((int) $service['is_closed'] === 1) {
            $this->fail('This service is closed and can no longer be modified.', [], 409);
        }
        return $service;
    }

    private function syncTotal(int $serviceId): void {
        $marked = (int) \Database::fetchColumn('SELECT COUNT(*) FROM attendance WHERE service_id = ?', [$serviceId]);
        \Database::update('services', ['total_count' => $marked], 'id = :id', [':id' => $serviceId]);
    }

    /* ------------------------------------------------------------------ */
    /* Giving                                                              */
    /* ------------------------------------------------------------------ */

    /** POST /api/admin/giving/record — record a manual (cash/POS/transfer) gift. */
    public function givingRecord(): void {
        $this->requireAdminApi();
        $this->requirePost();
        $this->guardCsrf();

        $amount = (float) $this->param('amount', 0);
        $type = (string) $this->param('giving_type', '');
        $method = (string) $this->param('giving_method', 'cash');
        $allowedTypes = ['tithe', 'offering', 'seed', 'project', 'welfare', 'mission', 'thanksgiving', 'vow', 'other'];
        $allowedMethods = ['cash', 'bank_transfer', 'pos', 'online', 'cheque'];
        $errors = [];
        if ($amount <= 0) { $errors['amount'] = 'Enter an amount greater than zero.'; }
        if (!in_array($type, $allowedTypes, true)) { $errors['giving_type'] = 'Select a valid giving type.'; }
        if (!in_array($method, $allowedMethods, true)) { $errors['giving_method'] = 'Select a valid method.'; }
        if ($errors) {
            $this->fail('Please correct the errors below.', $errors, 422);
        }

        $givingModel = new \App\Models\GivingModel();
        $memberId = (int) $this->param('member_id', 0);
        $givingDate = (string) $this->param('giving_date', date('Y-m-d'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $givingDate)) {
            $givingDate = date('Y-m-d');
        }

        $reference = $givingModel->reference();
        $id = $givingModel->create([
            'reference_no' => $reference,
            'member_id' => $memberId > 0 ? $memberId : null,
            'giver_name' => ($this->param('giver_name') ?: null),
            'giver_email' => ($this->param('giver_email') ?: null),
            'giver_phone' => ($this->param('giver_phone') ?: null),
            'amount' => $amount,
            'currency' => CURRENCY,
            'giving_type' => $type,
            'giving_method' => $method,
            'payment_status' => 'success',
            'description' => ($this->param('description') ?: null),
            'giving_date' => $givingDate,
            'recorded_by' => $this->userId,
        ]);

        \Audit::log('create', 'giving', 'Recorded manual giving ' . $reference . ' (' . Helpers::currency($amount) . ')');
        $this->ok(['id' => (int) $id, 'reference_no' => $reference], 'Giving recorded: ' . $reference);
    }

    /* ------------------------------------------------------------------ */
    /* Dashboard                                                           */
    /* ------------------------------------------------------------------ */

    /** GET /api/admin/dashboard/stats — refresh dashboard KPI cards. */
    public function dashboardStats(): void {
        $this->requireAdminApi();
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        $stats = [
            'total_members' => (int) \Database::fetchColumn('SELECT COUNT(*) FROM members'),
            'active_members' => (int) \Database::fetchColumn("SELECT COUNT(*) FROM members WHERE membership_status = 'active'"),
            'today_attendance' => (int) \Database::fetchColumn(
                'SELECT COALESCE(SUM(a.c),0) FROM (SELECT COUNT(*) c FROM attendance att JOIN services s ON s.id = att.service_id WHERE s.service_date = ?) a',
                [$today]
            ),
            'monthly_giving' => (float) \Database::fetchColumn(
                "SELECT COALESCE(SUM(amount),0) FROM giving WHERE payment_status = 'success' AND giving_date >= ?",
                [$monthStart]
            ),
            'pending_prayers' => (int) \Database::fetchColumn("SELECT COUNT(*) FROM prayer_requests WHERE status IN ('new','praying')"),
            'unread_messages' => (int) \Database::fetchColumn('SELECT COUNT(*) FROM contacts WHERE is_read = 0'),
        ];
        $this->ok(['stats' => $stats]);
    }

    /* ------------------------------------------------------------------ */
    /* Contact inbox                                                       */
    /* ------------------------------------------------------------------ */

    /** POST /api/admin/contact/reply — email a reply and log it. */
    public function contactReply(): void {
        $this->requireAdminApi();
        $this->requirePost();
        $this->guardCsrf();

        $id = (int) $this->param('id', 0);
        $reply = (string) $this->param('reply_text', '');
        $contact = $id > 0 ? \Database::fetchOne('SELECT * FROM contacts WHERE id = ?', [$id]) : null;
        if (!$contact) {
            $this->fail('Message not found.', [], 404);
        }
        if (strlen($reply) < 2) {
            $this->fail('Enter a reply message.', ['reply_text' => 'Reply is required.'], 422);
        }

        $subject = 'Re: ' . $contact['subject'];
        $body = nl2br(Helpers::escape($reply));
        $sent = false;
        try {
            $sent = (new \Mailer())->send($contact['email'], $subject, $body, true);
        } catch (\Throwable $e) {
            error_log('Contact reply mail failed: ' . $e->getMessage());
        }

        \Database::insert('sms_emails_log', [
            'type' => 'email',
            'recipient' => $contact['email'],
            'subject' => $subject,
            'body' => $reply,
            'status' => $sent ? 'sent' : 'pending',
            'sent_by' => $this->userId,
        ]);

        \Database::update('contacts', [
            'is_read' => 1,
            'is_replied' => 1,
            'reply_text' => $reply,
            'replied_by' => $this->userId,
            'replied_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $id]);

        \Audit::log('reply', 'contacts', 'Replied to contact message #' . $id);
        $this->ok(['queued' => !$sent], $sent ? 'Reply sent.' : 'Reply saved and queued for delivery.');
    }

    /* ------------------------------------------------------------------ */
    /* Notifications                                                       */
    /* ------------------------------------------------------------------ */

    /** GET /api/admin/notifications — current user's unread + recent notifications. */
    public function notifications(): void {
        $this->requireAdminApi();
        $rows = \Database::fetchAll(
            'SELECT id, type, title, message, link, is_read, created_at
             FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 20',
            [$this->userId]
        );
        $unread = (int) \Database::fetchColumn('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0', [$this->userId]);
        $this->ok(['notifications' => $rows, 'unread' => $unread]);
    }

    /** POST /api/admin/notifications/read — mark one or all notifications read. */
    public function notificationsRead(): void {
        $this->requireAdminApi();
        $this->requirePost();
        $this->guardCsrf();
        $id = (int) $this->param('id', 0);
        if ($id > 0) {
            \Database::update('notifications', ['is_read' => 1], 'id = :id AND user_id = :uid', [':id' => $id, ':uid' => $this->userId]);
        } else {
            \Database::update('notifications', ['is_read' => 1], 'user_id = :uid AND is_read = 0', [':uid' => $this->userId]);
        }
        $this->ok([], 'Notifications updated.');
    }
}
