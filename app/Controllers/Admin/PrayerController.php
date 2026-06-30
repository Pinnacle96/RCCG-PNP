<?php

namespace App\Controllers\Admin;

class PrayerController extends \Controller {
    private \App\Models\PrayerModel $prayers;

    private const CATEGORIES = ['healing', 'deliverance', 'finance', 'family', 'salvation', 'career', 'marriage', 'thanksgiving', 'others'];
    private const STATUSES = ['new', 'praying', 'answered', 'archived'];

    public function __construct() {
        parent::__construct();
        $this->prayers = new \App\Models\PrayerModel();
    }

    public function index(): void {
        $this->requireAdmin();

        $filters = [
            'status' => (string) $this->input('status', ''),
            'category' => (string) $this->input('category', ''),
            'q' => trim((string) $this->input('q', '')),
        ];
        $page = max(1, (int) $this->input('page', 1));
        $limit = ADMIN_ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $total = $this->prayers->totalFiltered($filters);

        $this->view('admin.prayer.index', [
            'title' => 'Prayer Requests',
            'page_title' => 'Prayer Requests',
            'requests' => $this->prayers->paginate($filters, $limit, $offset),
            'counts' => $this->prayers->statusCounts(),
            'filters' => $filters,
            'categories' => self::CATEGORIES,
            'statuses' => self::STATUSES,
            'page' => $page,
            'total' => $total,
            'totalPages' => max(1, (int) ceil($total / $limit)),
        ], 'admin');
    }

    public function show(): void {
        $this->requireAdmin();
        $request = $this->findOr404();

        $this->view('admin.prayer.show', [
            'title' => 'Prayer Request',
            'page_title' => 'Prayer Request',
            'request' => $request,
            'statuses' => self::STATUSES,
            'team' => \Database::fetchAll(
                "SELECT id, email FROM users WHERE role IN ('super_admin','admin','pastor','deacon','cell_leader') AND is_active = 1 ORDER BY email"
            ),
        ], 'admin');
    }

    public function update(): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $request = $this->findOr404();

        $status = (string) $this->input('status', $request['status']);
        if (!in_array($status, self::STATUSES, true)) {
            $status = $request['status'];
        }
        $assignedTo = (int) $this->input('assigned_to', 0);
        $answeredNote = trim((string) $this->input('answered_note', ''));

        $data = [
            'status' => $status,
            'is_answered' => $status === 'answered' ? 1 : 0,
            'answered_note' => $answeredNote === '' ? null : $answeredNote,
            'assigned_to' => $assignedTo > 0 ? $assignedTo : null,
        ];
        $this->prayers->update((int) $request['id'], $data);
        \Audit::log('update', 'prayer', 'Updated prayer request #' . $request['id'] . ' to ' . $status);

        $this->setFlash('success', 'Prayer request updated.');
        $this->redirect(BASE_URL . '/admin/prayer/view/' . $request['id']);
    }

    public function reply(): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $request = $this->findOr404();

        $message = trim((string) $this->input('reply_text', ''));
        if ($request['email'] === null || $request['email'] === '') {
            $this->setFlash('error', 'This requester did not provide an email address.');
            $this->redirect(BASE_URL . '/admin/prayer/view/' . $request['id']);
        }
        if (strlen($message) < 2) {
            $this->setFlash('error', 'Enter a reply message.');
            $this->redirect(BASE_URL . '/admin/prayer/view/' . $request['id']);
        }

        $subject = 'Re: ' . $request['subject'];
        $sent = false;
        try {
            $sent = (new \Mailer())->send($request['email'], $subject, nl2br(\Helpers::escape($message)), true);
        } catch (\Throwable $e) {
            error_log('Prayer reply mail failed: ' . $e->getMessage());
        }

        \Database::insert('sms_emails_log', [
            'type' => 'email',
            'recipient' => $request['email'],
            'subject' => $subject,
            'body' => $message,
            'status' => $sent ? 'sent' : 'pending',
            'sent_by' => $this->userId,
        ]);
        \Audit::log('reply', 'prayer', 'Replied to prayer request #' . $request['id']);

        $this->setFlash('success', $sent ? 'Reply sent to requester.' : 'Reply saved and queued for delivery.');
        $this->redirect(BASE_URL . '/admin/prayer/view/' . $request['id']);
    }

    public function delete(): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $request = $this->findOr404();
        $this->prayers->delete((int) $request['id']);
        \Audit::log('delete', 'prayer', 'Deleted prayer request #' . $request['id']);
        $this->setFlash('success', 'Prayer request deleted.');
        $this->redirect(BASE_URL . '/admin/prayer');
    }

    private function requireAdmin(): void {
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PASTOR, ROLE_DEACON]);
    }

    private function findOr404(): array {
        $id = (int) ($_GET['id'] ?? 0);
        $request = $id > 0 ? $this->prayers->find($id) : null;
        if (!$request) {
            http_response_code(404);
            $this->view('frontend.404', ['title' => 'Prayer Request Not Found'], 'public');
            exit;
        }
        return $request;
    }
}
