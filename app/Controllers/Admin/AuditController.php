<?php

namespace App\Controllers\Admin;

class AuditController extends \Controller {
    /** GET /admin/audit-log */
    public function index(): void {
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);

        $action = trim((string) $this->input('action', ''));
        $userId = (int) $this->input('user_id', 0);
        $from = $this->validDate((string) $this->input('from', ''));
        $to = $this->validDate((string) $this->input('to', ''));

        $where = [];
        $params = [];
        if ($action !== '') {
            $where[] = 'a.action = ?';
            $params[] = $action;
        }
        if ($userId > 0) {
            $where[] = 'a.user_id = ?';
            $params[] = $userId;
        }
        if ($from !== '') {
            $where[] = 'a.created_at >= ?';
            $params[] = $from . ' 00:00:00';
        }
        if ($to !== '') {
            $where[] = 'a.created_at <= ?';
            $params[] = $to . ' 23:59:59';
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $rows = \Database::fetchAll(
            "SELECT a.id, a.action, a.module, a.description, a.ip_address, a.created_at, u.email
             FROM audit_log a
             LEFT JOIN users u ON u.id = a.user_id
             $whereSql
             ORDER BY a.created_at DESC, a.id DESC
             LIMIT 500",
            $params
        );

        $this->view('admin.audit-log.index', [
            'title' => 'Audit Log',
            'page_title' => 'Audit Log',
            'rows' => $rows,
            'actions' => \Database::fetchAll('SELECT DISTINCT action FROM audit_log ORDER BY action'),
            'users' => \Database::fetchAll('SELECT DISTINCT u.id AS value, u.email AS label FROM audit_log a INNER JOIN users u ON u.id = a.user_id ORDER BY u.email'),
            'filters' => ['action' => $action, 'user_id' => $userId, 'from' => $from, 'to' => $to],
        ], 'admin');
    }

    private function validDate(string $value): string {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : '';
    }
}
