<?php

namespace App\Controllers\Admin;

class AttendanceController extends \Controller {
    private \App\Models\ServiceModel $services;
    private \App\Models\AttendanceModel $attendance;

    public function __construct() {
        parent::__construct();
        $this->services = new \App\Models\ServiceModel();
        $this->attendance = new \App\Models\AttendanceModel();
    }

    public function index(): void {
        $this->requireAdmin();

        $search = trim((string) $this->input('q', ''));
        $page = max(1, (int) $this->input('page', 1));
        $limit = ADMIN_ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $total = $this->services->total($search);

        $this->view('admin.attendance.index', [
            'title' => 'Attendance',
            'page_title' => 'Attendance',
            'services' => $this->services->paginate($search, $limit, $offset),
            'search' => $search,
            'page' => $page,
            'total' => $total,
            'totalPages' => max(1, (int) ceil($total / $limit)),
        ], 'admin');
    }

    public function create(): void {
        $this->requireAdmin();
        $this->view('admin.attendance.form', [
            'title' => 'Create Service',
            'page_title' => 'Create Service',
            'service' => null,
            'action' => BASE_URL . '/admin/attendance',
            'mode' => 'create',
        ], 'admin');
    }

    public function store(): void {
        $this->requireAdmin();
        $this->verifyCsrf();

        $data = $this->servicePayload();
        $data['created_by'] = $this->userId;
        $id = $this->services->create($data);

        $this->setFlash('success', 'Service created successfully.');
        $this->redirect(BASE_URL . '/admin/attendance/view/' . $id);
    }

    public function edit(): void {
        $this->requireAdmin();
        $service = $this->findServiceOr404();

        $this->view('admin.attendance.form', [
            'title' => 'Edit Service',
            'page_title' => 'Edit Service',
            'service' => $service,
            'action' => BASE_URL . '/admin/attendance/edit/' . $service['id'],
            'mode' => 'edit',
        ], 'admin');
    }

    public function update(): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $service = $this->findServiceOr404();

        $this->services->update((int) $service['id'], $this->servicePayload());
        $this->setFlash('success', 'Service updated successfully.');
        $this->redirect(BASE_URL . '/admin/attendance/view/' . $service['id']);
    }

    public function show(): void {
        $this->requireAdmin();
        $service = $this->findServiceOr404(true);
        $search = trim((string) $this->input('q', ''));

        $this->view('admin.attendance.show', [
            'title' => 'Service Attendance',
            'page_title' => 'Service Attendance',
            'service' => $service,
            'present' => $this->attendance->presentForService((int) $service['id']),
            'members' => $this->attendance->searchableMembers((int) $service['id'], $search),
            'methodTotals' => $this->attendance->totalsByMethod((int) $service['id']),
            'search' => $search,
        ], 'admin');
    }

    public function mark(): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $service = $this->findServiceOr404(true);
        $memberId = (int) $this->input('member_id', 0);

        if ($memberId <= 0) {
            $this->setFlash('error', 'Please select a member to mark present.');
            $this->redirect(BASE_URL . '/admin/attendance/view/' . $service['id']);
        }

        $this->attendance->mark((int) $service['id'], $memberId, (int) $this->userId);
        $this->syncServiceTotal((int) $service['id']);
        $this->setFlash('success', 'Attendance marked successfully.');
        $this->redirect(BASE_URL . '/admin/attendance/view/' . $service['id']);
    }

    public function remove(): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $service = $this->findServiceOr404(true);
        $memberId = (int) $this->input('member_id', 0);

        if ($memberId > 0) {
            $this->attendance->remove((int) $service['id'], $memberId);
            $this->syncServiceTotal((int) $service['id']);
            $this->setFlash('success', 'Attendance removed.');
        }

        $this->redirect(BASE_URL . '/admin/attendance/view/' . $service['id']);
    }

    private function requireAdmin(): void {
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PASTOR, ROLE_DEACON]);
    }

    private function findServiceOr404(bool $withStats = false): array {
        $id = (int) ($_GET['id'] ?? 0);
        $service = $id > 0
            ? ($withStats ? $this->services->findWithAttendanceStats($id) : $this->services->find($id))
            : null;

        if (!$service) {
            http_response_code(404);
            $this->view('frontend.404', ['title' => 'Service Not Found'], 'public');
            exit;
        }

        return $service;
    }

    private function servicePayload(): array {
        $serviceDate = trim((string) $this->input('service_date', ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $serviceDate)) {
            $this->setFlash('error', 'Service date is required.');
            $this->redirect(BASE_URL . '/admin/attendance/add');
        }

        return [
            'service_type' => $this->enumValue('service_type', ['sunday_first', 'sunday_second', 'wednesday', 'friday', 'special', 'cell'], 'sunday_first'),
            'service_date' => $serviceDate,
            'theme' => $this->nullableString('theme'),
            'preacher' => $this->nullableString('preacher'),
            'men_count' => max(0, (int) $this->input('men_count', 0)),
            'women_count' => max(0, (int) $this->input('women_count', 0)),
            'children_count' => max(0, (int) $this->input('children_count', 0)),
            'visitors_count' => max(0, (int) $this->input('visitors_count', 0)),
            'offering_amount' => max(0, (float) $this->input('offering_amount', 0)),
            'tithe_amount' => max(0, (float) $this->input('tithe_amount', 0)),
            'notes' => $this->nullableString('notes'),
            'is_closed' => $this->input('is_closed') ? 1 : 0,
        ];
    }

    private function syncServiceTotal(int $serviceId): void {
        $marked = (int) \Database::fetchColumn('SELECT COUNT(*) FROM attendance WHERE service_id = ?', [$serviceId]);
        \Database::update('services', ['total_count' => $marked], 'id = :id', [':id' => $serviceId]);
    }

    private function nullableString(string $key): ?string {
        $value = trim((string) $this->input($key, ''));
        return $value === '' ? null : $value;
    }

    private function enumValue(string $key, array $allowed, string $default): string {
        $value = trim((string) $this->input($key, $default));
        return in_array($value, $allowed, true) ? $value : $default;
    }
}
