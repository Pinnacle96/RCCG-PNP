<?php

namespace App\Controllers\Auth;

class LoginController extends \Controller {
    public function showLogin(): void {
        $this->view('auth.login', ['title' => 'Login'], 'auth');
    }

    public function login(): void {
        $this->verifyCsrf();

        $email = strtolower((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');
        $remember = (bool) $this->input('remember', false);

        if (\Auth::isLocked($email)) {
            $this->setFlash('error', 'Too many failed login attempts. Please try again in 15 minutes.');
            $this->redirect(BASE_URL . '/login');
        }

        if (\Auth::attempt($email, $password, $remember)) {
            $user = \Auth::user();
            $target = ($user['role'] ?? ROLE_MEMBER) === ROLE_MEMBER ? BASE_URL . '/portal' : BASE_URL . '/admin';
            $this->redirect($target);
        }

        $this->setFlash('error', 'Invalid email or password.');
        $this->redirect(BASE_URL . '/login');
    }

    public function logout(): void {
        \Auth::logout();
        $this->redirect(BASE_URL . '/login');
    }
}
