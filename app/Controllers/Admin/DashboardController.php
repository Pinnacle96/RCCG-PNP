<?php

namespace App\Controllers\Admin;

class DashboardController extends \Controller {
    public function index(): void {
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PASTOR, ROLE_DEACON]);

        $this->view('admin.dashboard.index', [
            'title' => 'Admin Dashboard',
            'page_title' => 'Dashboard',
            'stats' => [
                'members' => (int) \Database::fetchColumn('SELECT COUNT(*) FROM members'),
                'sermons' => (int) \Database::fetchColumn('SELECT COUNT(*) FROM sermons'),
                'events' => (int) \Database::fetchColumn('SELECT COUNT(*) FROM events'),
                'giving' => (float) \Database::fetchColumn("SELECT COALESCE(SUM(amount), 0) FROM giving WHERE payment_status = 'success'"),
                'services' => (int) \Database::fetchColumn('SELECT COUNT(*) FROM services'),
                'attendance' => (int) \Database::fetchColumn('SELECT COUNT(*) FROM attendance'),
            ],
        ], 'admin');
    }
}
