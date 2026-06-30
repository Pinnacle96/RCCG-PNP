<?php

namespace App\Controllers\Auth;

class PasswordController extends \Controller {
    public function showForgot(): void {
        $this->view('auth.forgot-password', ['title' => 'Forgot Password'], 'auth');
    }

    public function sendReset(): void {
        $this->verifyCsrf();

        $email = strtolower((string) $this->input('email', ''));
        $userModel = new \App\Models\UserModel();
        $user = filter_var($email, FILTER_VALIDATE_EMAIL) ? $userModel->findByEmail($email) : null;

        if ($user) {
            $rawToken = \Auth::generatePasswordResetToken((int) $user['id']);
            $token = $this->encodeResetToken((int) $user['id'], $rawToken);
            $resetUrl = BASE_URL . '/reset-password/' . rawurlencode($token);
            $this->queueResetEmail($email, $resetUrl);
        }

        $this->setFlash('success', 'If that email exists, a password reset link has been prepared.');
        $this->redirect(BASE_URL . '/forgot-password');
    }

    public function showReset(): void {
        $token = (string) ($_GET['token'] ?? '');
        [$userId, $rawToken] = $this->decodeResetToken($token);

        if (!$userId || !$rawToken || !\Auth::verifyPasswordResetToken($userId, $rawToken)) {
            $this->setFlash('error', 'That password reset link is invalid or expired.');
            $this->redirect(BASE_URL . '/forgot-password');
        }

        $this->view('auth.reset-password', [
            'title' => 'Reset Password',
            'resetToken' => $token,
        ], 'auth');
    }

    public function reset(): void {
        $this->verifyCsrf();

        $token = (string) $this->input('token', '');
        $password = (string) $this->input('password', '');
        $confirm = (string) $this->input('password_confirmation', '');
        [$userId, $rawToken] = $this->decodeResetToken($token);

        if (!$userId || !$rawToken || !\Auth::verifyPasswordResetToken($userId, $rawToken)) {
            $this->setFlash('error', 'That password reset link is invalid or expired.');
            $this->redirect(BASE_URL . '/forgot-password');
        }

        if (strlen($password) < 8 || $password !== $confirm) {
            $this->setFlash('error', 'Password must be at least 8 characters and match the confirmation.');
            $this->redirect(BASE_URL . '/reset-password/' . rawurlencode($token));
        }

        \Auth::resetPassword($userId, $password);
        $this->setFlash('success', 'Password reset successfully. Please sign in.');
        $this->redirect(BASE_URL . '/login');
    }

    private function encodeResetToken(int $userId, string $rawToken): string {
        $encodedUser = rtrim(strtr(base64_encode((string) $userId), '+/', '-_'), '=');
        return $encodedUser . '.' . $rawToken;
    }

    private function decodeResetToken(string $token): array {
        if (!str_contains($token, '.')) {
            return [null, null];
        }

        [$encodedUser, $rawToken] = explode('.', $token, 2);
        $userId = base64_decode(strtr($encodedUser, '-_', '+/'), true);

        if ($userId === false || !ctype_digit($userId) || !preg_match('/^[a-f0-9]{64}$/', $rawToken)) {
            return [null, null];
        }

        return [(int) $userId, $rawToken];
    }

    private function queueResetEmail(string $email, string $resetUrl): void {
        $subject = 'Reset your ' . SITE_NAME . ' password';
        $body = "Use this link to reset your password: {$resetUrl}";

        try {
            \Database::insert('sms_emails_log', [
                'type' => 'email',
                'recipient' => $email,
                'subject' => $subject,
                'body' => $body,
                'status' => 'pending',
            ]);
        } catch (\Throwable $e) {
            error_log('Password reset email queue failed: ' . $e->getMessage());
        }

        // Dev convenience only: never persist reset links (which carry the
        // token) to disk in production. The link is delivered via email above.
        if (DEBUG) {
            error_log('Password reset link for ' . $email . ': ' . $resetUrl, 3, LOG_PATH . '/password-reset.log');
        }
    }
}
