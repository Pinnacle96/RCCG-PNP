<?php
/**
 * Authentication Helper
 * Session and role management, CSRF, rate limiting, password reset
 */

class Auth {
    /**
     * Check if user is logged in
     */
    public static function check(): bool {
        return !empty($_SESSION['user_id']);
    }

    /**
     * Get current user ID
     */
    public static function userId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user array
     */
    public static function user(): ?array {
        if (!self::check()) {
            return null;
        }
        if (!isset($_SESSION['user'])) {
            $userModel = new \App\Models\UserModel();
            $_SESSION['user'] = $userModel->find(self::userId());
        }
        return $_SESSION['user'];
    }

    /**
     * Get current user role
     */
    public static function role(): ?string {
        $user = self::user();
        return $user['role'] ?? null;
    }

    /**
     * Check if user has a specific role
     */
    public static function hasRole(string $role): bool {
        return self::role() === $role;
    }

    /**
     * Login user
     */
    public static function login(int $userId, bool $remember = false): void {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        unset($_SESSION['user']);

        if ($remember) {
            $token = bin2hex(random_bytes(50));
            $userModel = new \App\Models\UserModel();
            $userModel->update($userId, [
                'remember_token' => password_hash($token, PASSWORD_DEFAULT),
                'last_login' => date('Y-m-d H:i:s')
            ]);
            setcookie('remember_me', $token, [
                'expires' => time() + (REMEMBER_ME_DAYS * 24 * 60 * 60),
                'path' => '/',
                'domain' => '',
                'secure' => isHttps(), // transmit only over HTTPS in production
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        } else {
            $_SESSION['last_login'] = time();
        }
    }

    /**
     * Attempt login with email/password
     */
    public static function attempt(string $email, string $password, bool $remember = false): bool {
        $email = strtolower(trim($email));

        if (self::isLocked($email)) {
            return false;
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->findBy('email', $email);

        if (!$user) {
            self::logFailedAttempt($email);
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            self::logFailedAttempt($email);
            return false;
        }

        if (!$user['is_active']) {
            self::logFailedAttempt($email);
            return false;
        }

        self::clearFailedAttempts($email);
        self::login($user['id'], $remember);
        return true;
    }

    /**
     * Logout user
     */
    public static function logout(): void {
        unset($_SESSION['user_id'], $_SESSION['user']);
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, '/');
        }
        session_destroy();
    }

    /**
     * Generate CSRF token
     */
    public static function csrf(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCsrf(): bool {
        $headerName = 'HTTP_' . strtoupper(str_replace('-', '_', CSRF_HEADER_NAME));
        $token = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER[$headerName] ?? null;
        if (!$token || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate password reset token
     */
    public static function generatePasswordResetToken(int $userId): string {
        $token = bin2hex(random_bytes(32));
        $hash = password_hash($token, PASSWORD_DEFAULT);
        $expires = date('Y-m-d H:i:s', time() + PASSWORD_RESET_EXPIRY);

        $userModel = new \App\Models\UserModel();
        $userModel->update($userId, [
            'reset_token' => $hash,
            'reset_expires' => $expires
        ]);

        return $token;
    }

    /**
     * Verify password reset token
     */
    public static function verifyPasswordResetToken(int $userId, string $token): bool {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user || !$user['reset_token']) {
            return false;
        }

        if (strtotime($user['reset_expires']) < time()) {
            return false;
        }

        return password_verify($token, $user['reset_token']);
    }

    /**
     * Reset password
     */
    public static function resetPassword(int $userId, string $password): bool {
        $userModel = new \App\Models\UserModel();
        return $userModel->update($userId, [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_expires' => null
        ]);
    }

    /**
     * Log failed login attempt
     */
    private static function logFailedAttempt(string $email): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        try {
            Database::insert('login_attempts', [
                'email' => strtolower(trim($email)),
                'ip_address' => $ip,
                'success' => 0,
                'attempted_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            error_log('Failed login attempt: ' . json_encode([
                'email' => $email,
                'ip' => $ip,
                'timestamp' => date('Y-m-d H:i:s'),
            ]));
        }
    }

    /**
     * Clear failed attempts for email
     */
    private static function clearFailedAttempts(string $email): void {
        try {
            Database::delete('login_attempts', 'email = :email AND success = 0', [':email' => strtolower(trim($email))]);
            Database::insert('login_attempts', [
                'email' => strtolower(trim($email)),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'success' => 1,
                'attempted_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            error_log('Unable to clear failed login attempts: ' . $e->getMessage());
        }
    }

    /**
     * Check if user is locked out
     */
    public static function isLocked(string $email): bool {
        try {
            $cutoff = date('Y-m-d H:i:s', time() - (LOGIN_LOCKOUT_MINUTES * 60));
            $attempts = (int) Database::fetchColumn(
                'SELECT COUNT(*) FROM login_attempts WHERE email = :email AND success = 0 AND attempted_at >= :cutoff',
                [
                    ':email' => strtolower(trim($email)),
                    ':cutoff' => $cutoff,
                ]
            );
            return $attempts >= MAX_LOGIN_ATTEMPTS;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
