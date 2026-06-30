<?php

namespace App\Controllers\Frontend;

class ContactController extends \Controller {
    public function index(): void {
        $this->view('frontend.contact', [
            'title' => 'Contact',
            'description' => 'Contact ' . \Settings::get('site_name', SITE_NAME),
        ], 'public');
    }

    public function send(): void {
        $this->verifyCsrf();

        if (!\Recaptcha::verify((string) $this->input('recaptcha_token', ''))) {
            $this->setFlash('error', 'Spam check failed. Please try again.');
            $this->redirect(BASE_URL . '/contact');
        }

        $name = trim((string) $this->input('name', ''));
        $email = strtolower(trim((string) $this->input('email', '')));
        $phone = trim((string) $this->input('phone', ''));
        $subject = trim((string) $this->input('subject', ''));
        $message = trim((string) $this->input('message', ''));

        $errors = [];
        if ($name === '') {
            $errors[] = 'Name is required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email is required.';
        }
        if ($subject === '') {
            $errors[] = 'Subject is required.';
        }
        if (strlen($message) < 10) {
            $errors[] = 'Message must be at least 10 characters.';
        }

        if ($errors) {
            $this->setFlash('error', implode(' ', $errors));
            $this->redirect(BASE_URL . '/contact');
        }

        (new \App\Models\ContactModel())->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone === '' ? null : $phone,
            'subject' => $subject,
            'message' => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $this->setFlash('success', 'Thank you. Your message has been received.');
        $this->redirect(BASE_URL . '/contact');
    }
}
