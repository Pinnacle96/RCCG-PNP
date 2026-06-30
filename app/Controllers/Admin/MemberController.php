<?php

namespace App\Controllers\Admin;

class MemberController extends \Controller {
    private \App\Models\MemberModel $members;

    public function __construct() {
        parent::__construct();
        $this->members = new \App\Models\MemberModel();
    }

    public function index(): void {
        $this->requireAdmin();

        $search = trim((string) $this->input('q', ''));
        $page = max(1, (int) $this->input('page', 1));
        $limit = ADMIN_ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $total = $this->members->total($search);

        $this->view('admin.members.index', [
            'title' => 'Members',
            'page_title' => 'Members',
            'members' => $this->members->paginate($search, $limit, $offset),
            'search' => $search,
            'page' => $page,
            'total' => $total,
            'totalPages' => max(1, (int) ceil($total / $limit)),
            'statusCounts' => $this->members->statusCounts(),
        ], 'admin');
    }

    /** GET /admin/members/export?format=csv|xlsx — export the (filtered) member list. */
    public function export(): void {
        $this->requireAdmin();
        $search = trim((string) $this->input('q', ''));
        $rows = $this->members->paginate($search, 100000, 0);

        $headers = ['Code', 'First Name', 'Last Name', 'Gender', 'Phone', 'Email', 'Status', 'Type', 'Joined'];
        $data = array_map(static function (array $m): array {
            return [
                $m['member_code'] ?? '',
                $m['first_name'] ?? '',
                $m['last_name'] ?? '',
                $m['gender'] ?? '',
                $m['phone'] ?? '',
                $m['email'] ?? '',
                $m['membership_status'] ?? '',
                $m['membership_type'] ?? '',
                $m['join_date'] ?? '',
            ];
        }, $rows);

        \Audit::log('export', 'members', 'Exported member list (' . count($data) . ' rows)');

        if (strtolower((string) $this->input('format', 'csv')) === 'xlsx') {
            \Spreadsheet::stream($headers, $data, 'members-' . date('Ymd-His') . '.xlsx', 'Members');
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="members-' . date('Ymd-His') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, $headers);
        foreach ($data as $r) {
            fputcsv($out, $r);
        }
        fclose($out);
        exit;
    }

    public function create(): void {
        $this->requireAdmin();

        $this->view('admin.members.form', [
            'title' => 'Add Member',
            'page_title' => 'Add Member',
            'member' => null,
            'action' => BASE_URL . '/admin/members',
            'mode' => 'create',
        ], 'admin');
    }

    public function store(): void {
        $this->requireAdmin();
        $this->verifyCsrf();

        $data = $this->validatedMemberData();
        if ($data === null) {
            $this->redirect(BASE_URL . '/admin/members/add');
        }

        $data['member_code'] = $this->members->generateMemberCode();
        $data['created_by'] = $this->userId;
        $memberId = $this->members->create($data);

        $this->setFlash('success', 'Member created successfully.');
        $this->redirect(BASE_URL . '/admin/members/view/' . $memberId);
    }

    public function edit(): void {
        $this->requireAdmin();
        $member = $this->findMemberOr404();

        $this->view('admin.members.form', [
            'title' => 'Edit Member',
            'page_title' => 'Edit Member',
            'member' => $member,
            'action' => BASE_URL . '/admin/members/edit/' . $member['id'],
            'mode' => 'edit',
        ], 'admin');
    }

    public function update(): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $member = $this->findMemberOr404();

        $data = $this->validatedMemberData((int) $member['id']);
        if ($data === null) {
            $this->redirect(BASE_URL . '/admin/members/edit/' . $member['id']);
        }

        $this->members->update((int) $member['id'], $data);
        $this->setFlash('success', 'Member updated successfully.');
        $this->redirect(BASE_URL . '/admin/members/view/' . $member['id']);
    }

    public function viewMember(): void {
        $this->requireAdmin();
        $member = $this->findMemberOr404();

        $this->view('admin.members.view', [
            'title' => $member['first_name'] . ' ' . $member['last_name'],
            'page_title' => 'Member Profile',
            'member' => $member,
            'qr' => \Qr::dataUri((string) $member['member_code']),
        ], 'admin');
    }

    private function requireAdmin(): void {
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PASTOR, ROLE_DEACON]);
    }

    private function findMemberOr404(): array {
        $id = (int) ($_GET['id'] ?? 0);
        $member = $id > 0 ? $this->members->find($id) : null;

        if (!$member) {
            http_response_code(404);
            $this->view('frontend.404', ['title' => 'Member Not Found'], 'public');
            exit;
        }

        return $member;
    }

    private function validatedMemberData(?int $memberId = null): ?array {
        $data = [
            'first_name' => trim((string) $this->input('first_name', '')),
            'last_name' => trim((string) $this->input('last_name', '')),
            'middle_name' => $this->nullableString('middle_name'),
            'gender' => (string) $this->input('gender', ''),
            'date_of_birth' => $this->nullableDate('date_of_birth'),
            'phone' => $this->nullableString('phone'),
            'alt_phone' => $this->nullableString('alt_phone'),
            'email' => $this->nullableString('email'),
            'address' => $this->nullableString('address'),
            'state_of_origin' => $this->nullableString('state_of_origin'),
            'occupation' => $this->nullableString('occupation'),
            'marital_status' => $this->nullableEnum('marital_status', ['single', 'married', 'divorced', 'widowed']),
            'spouse_name' => $this->nullableString('spouse_name'),
            'wedding_anniversary' => $this->nullableDate('wedding_anniversary'),
            'membership_type' => $this->enumValue('membership_type', ['full', 'associate', 'worker', 'junior'], 'full'),
            'membership_status' => $this->enumValue('membership_status', ['active', 'inactive', 'transferred', 'deceased'], 'active'),
            'join_date' => $this->nullableDate('join_date') ?? date('Y-m-d'),
            'baptism_date' => $this->nullableDate('baptism_date'),
            'water_baptized' => $this->checkboxValue('water_baptized'),
            'holy_ghost_baptized' => $this->checkboxValue('holy_ghost_baptized'),
            'emergency_contact' => $this->nullableString('emergency_contact'),
            'emergency_phone' => $this->nullableString('emergency_phone'),
            'notes' => $this->nullableString('notes'),
        ];

        $errors = [];
        if ($data['first_name'] === '') {
            $errors[] = 'First name is required.';
        }
        if ($data['last_name'] === '') {
            $errors[] = 'Last name is required.';
        }
        if (!in_array($data['gender'], ['male', 'female'], true)) {
            $errors[] = 'Gender is required.';
        }
        if ($data['email'] !== null && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email must be valid.';
        }
        if ($data['email'] !== null) {
            $existing = $this->members->findBy('email', $data['email']);
            if ($existing && (!$memberId || (int) $existing['id'] !== $memberId)) {
                $errors[] = 'Another member already uses this email.';
            }
        }

        if ($errors) {
            $this->setFlash('error', implode(' ', $errors));
            return null;
        }

        // Profile photo (optional). On edit, omit the key when no new file so the existing photo is kept.
        if (!empty($_FILES['profile_photo']['name'])) {
            $uploader = new \Uploader(UPLOAD_PATH, ALLOWED_IMAGE_TYPES, MAX_IMAGE_SIZE);
            $stored = $uploader->upload('profile_photo', 'members/');
            if ($stored === null) {
                $this->setFlash('error', 'Profile photo: ' . implode(' ', $uploader->getErrors()));
                return null;
            }
            $data['profile_photo'] = $stored;
        }

        return $data;
    }

    private function nullableString(string $key): ?string {
        $value = trim((string) $this->input($key, ''));
        return $value === '' ? null : $value;
    }

    private function nullableDate(string $key): ?string {
        $value = trim((string) $this->input($key, ''));
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
    }

    private function nullableEnum(string $key, array $allowed): ?string {
        $value = trim((string) $this->input($key, ''));
        return in_array($value, $allowed, true) ? $value : null;
    }

    private function enumValue(string $key, array $allowed, string $default): string {
        $value = trim((string) $this->input($key, $default));
        return in_array($value, $allowed, true) ? $value : $default;
    }

    private function checkboxValue(string $key): int {
        return $this->input($key) ? 1 : 0;
    }
}
