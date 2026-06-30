<?php

namespace App\Controllers\Auth;

class RegisterController extends \Controller {
    public function showRegister(): void {
        $this->view('auth.register', ['title' => 'Register'], 'auth');
    }

    public function register(): void {
        $this->verifyCsrf();

        $email = strtolower((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');
        $confirm = (string) $this->input('password_confirmation', '');

        $validator = new \Validator();
        $validator->required('email', $email, 'Email');
        $validator->email('email', $email, 'Email');
        $validator->min('password', $password, 8, 'Password');

        if ($password !== $confirm) {
            $errors = $validator->errors();
            $errors['password_confirmation'] = 'Password confirmation does not match';
        } else {
            $errors = $validator->errors();
        }

        $userModel = new \App\Models\UserModel();
        if ($userModel->findByEmail($email)) {
            $errors['email'] = 'An account already exists for this email address';
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode(' ', $errors));
            $this->redirect(BASE_URL . '/register');
        }

        $member = (new \App\Models\MemberModel())->findBy('email', $email);
        $userId = $userModel->createUser($email, $password, ROLE_MEMBER, $member ? (int) $member['id'] : null);

        $siteName = \Settings::get('site_name', SITE_NAME);
        $greeting = $member ? $member['first_name'] : 'Friend';
        \Queue::email(
            $email,
            'Welcome to ' . $siteName,
            '<p>Dear ' . \Helpers::escape($greeting) . ',</p>'
            . '<p>Welcome to ' . \Helpers::escape($siteName) . '! Your member portal account has been created. '
            . 'You can sign in any time to view your giving history, attendance, and more.</p>'
            . '<p>God bless you,<br>' . \Helpers::escape($siteName) . '</p>'
        );

        \Auth::login((int) $userId);
        $this->setFlash('success', 'Your account has been created.');
        $this->redirect(BASE_URL . '/portal');
    }
}
