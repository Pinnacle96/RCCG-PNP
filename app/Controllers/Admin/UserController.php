<?php

namespace App\Controllers\Admin;

class UserController extends \Controller {
    private const ROLES = [ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PASTOR, ROLE_DEACON, ROLE_CELL_LEADER, ROLE_MEMBER];

    private \App\Models\UserModel $users;

    public function __construct() {
        parent::__construct();
        $this->users = new \App\Models\UserModel();
    }

    /** GET /admin/users — list all accounts. */
    public function index(): void {
        $this->requireManage();
        $rows = \Database::fetchAll(
            "SELECT u.id, u.email, u.role, u.is_active, u.last_login, u.created_at,
                    CONCAT(m.first_name, ' ', m.last_name) AS member_name
             FROM users u
             LEFT JOIN members m ON m.id = u.member_id
             ORDER BY u.created_at DESC, u.id DESC"
        );

        $this->view('admin.users.index', [
            'title' => 'Users & Roles',
            'page_title' => 'Users & Roles',
            'rows' => $rows,
            'roles' => self::ROLES,
        ], 'admin');
    }

    /** GET /admin/users/add */
    public function create(): void {
        $this->requireManage();
        $this->view('admin.users.form', [
            'title' => 'Add User',
            'page_title' => 'Add User',
            'roles' => self::ROLES,
            'user' => null,
            'members' => $this->memberOptions(),
        ], 'admin');
    }

    /** POST /admin/users */
    public function store(): void {
        $this->requireManage();
        $this->verifyCsrf();

        $email = strtolower(trim((string) $this->input('email', '')));
        $role = (string) $this->input('role', ROLE_MEMBER);
        $password = (string) $this->input('password', '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($role, self::ROLES, true) || strlen($password) < 8) {
            $this->setFlash('error', 'Provide a valid email, role, and a password of at least 8 characters.');
            $this->redirect(BASE_URL . '/admin/users/add');
        }
        if ($this->users->findByEmail($email)) {
            $this->setFlash('error', 'A user with that email already exists.');
            $this->redirect(BASE_URL . '/admin/users/add');
        }

        $memberId = (int) $this->input('member_id', 0);
        $id = $this->users->createUser($email, $password, $role, $memberId > 0 ? $memberId : null);
        if (!(int) $this->input('is_active', 1)) {
            $this->users->update((int) $id, ['is_active' => 0]);
        }

        \Audit::log('create', 'users', 'Created user ' . $email . ' (' . $role . ')');
        $this->setFlash('success', 'User created.');
        $this->redirect(BASE_URL . '/admin/users');
    }

    /** GET /admin/users/edit/{id} */
    public function edit(): void {
        $this->requireManage();
        $user = $this->findOr404();
        $this->view('admin.users.form', [
            'title' => 'Edit User',
            'page_title' => 'Edit User',
            'roles' => self::ROLES,
            'user' => $user,
            'members' => $this->memberOptions(),
        ], 'admin');
    }

    /** POST /admin/users/edit/{id} */
    public function update(): void {
        $this->requireManage();
        $this->verifyCsrf();
        $user = $this->findOr404();

        $email = strtolower(trim((string) $this->input('email', '')));
        $role = (string) $this->input('role', $user['role']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($role, self::ROLES, true)) {
            $this->setFlash('error', 'Provide a valid email and role.');
            $this->redirect(BASE_URL . '/admin/users/edit/' . (int) $user['id']);
        }

        $existing = $this->users->findByEmail($email);
        if ($existing && (int) $existing['id'] !== (int) $user['id']) {
            $this->setFlash('error', 'Another user already uses that email.');
            $this->redirect(BASE_URL . '/admin/users/edit/' . (int) $user['id']);
        }

        // Guard: do not let the last active super admin be demoted or disabled.
        $isActive = (int) $this->input('is_active', 0);
        if ($user['role'] === ROLE_SUPER_ADMIN && ($role !== ROLE_SUPER_ADMIN || !$isActive) && $this->lastActiveSuperAdmin((int) $user['id'])) {
            $this->setFlash('error', 'You cannot demote or disable the last active super admin.');
            $this->redirect(BASE_URL . '/admin/users/edit/' . (int) $user['id']);
        }

        $data = ['email' => $email, 'role' => $role, 'is_active' => $isActive ? 1 : 0];

        $password = (string) $this->input('password', '');
        if ($password !== '') {
            if (strlen($password) < 8) {
                $this->setFlash('error', 'New password must be at least 8 characters.');
                $this->redirect(BASE_URL . '/admin/users/edit/' . (int) $user['id']);
            }
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->users->update((int) $user['id'], $data);
        \Audit::log('update', 'users', 'Updated user ' . $email);
        $this->setFlash('success', 'User updated.');
        $this->redirect(BASE_URL . '/admin/users');
    }

    /** POST /admin/users/delete/{id} */
    public function delete(): void {
        $this->requireManage();
        $this->verifyCsrf();
        $user = $this->findOr404();

        if ((int) $user['id'] === (int) $this->userId) {
            $this->setFlash('error', 'You cannot delete your own account.');
            $this->redirect(BASE_URL . '/admin/users');
        }
        if ($user['role'] === ROLE_SUPER_ADMIN && $this->lastActiveSuperAdmin((int) $user['id'])) {
            $this->setFlash('error', 'You cannot delete the last active super admin.');
            $this->redirect(BASE_URL . '/admin/users');
        }

        $this->users->delete((int) $user['id']);
        \Audit::log('delete', 'users', 'Deleted user ' . $user['email']);
        $this->setFlash('success', 'User deleted.');
        $this->redirect(BASE_URL . '/admin/users');
    }

    /* ------------------------------------------------------------------ */

    private function lastActiveSuperAdmin(int $excludeId): bool {
        $others = (int) \Database::fetchColumn(
            "SELECT COUNT(*) FROM users WHERE role = 'super_admin' AND is_active = 1 AND id != ?",
            [$excludeId]
        );
        return $others === 0;
    }

    private function findOr404(): array {
        $id = (int) ($_GET['id'] ?? 0);
        $user = $id > 0 ? $this->users->find($id) : null;
        if (!$user) {
            http_response_code(404);
            $this->view('frontend.404', ['title' => 'User Not Found'], 'public');
            exit;
        }
        return $user;
    }

    private function requireManage(): void {
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
    }

    /** Members not yet linked to a login account, plus the one already linked when editing. */
    private function memberOptions(): array {
        return \Database::fetchAll(
            "SELECT m.id AS value, CONCAT(m.first_name, ' ', m.last_name, ' (', m.member_code, ')') AS label
             FROM members m
             ORDER BY m.first_name, m.last_name"
        );
    }
}
