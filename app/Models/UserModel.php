<?php

namespace App\Models;

class UserModel extends \Model {
    protected string $table = 'users';

    public function findByEmail(string $email): ?array {
        return $this->findBy('email', $email);
    }

    public function createUser(string $email, string $password, string $role = ROLE_MEMBER, ?int $memberId = null): string {
        return $this->create([
            'member_id' => $memberId,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'is_active' => 1,
        ]);
    }

    public function touchLogin(int $userId): int {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }
}
