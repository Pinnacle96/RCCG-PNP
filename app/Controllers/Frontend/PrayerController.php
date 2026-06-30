<?php

namespace App\Controllers\Frontend;

class PrayerController extends \Controller {
    public function index(): void {
        $this->view('frontend.prayer', [
            'title' => 'Prayer Requests',
            'requests' => (new \App\Models\PrayerModel())->publicRequests(),
        ], 'public');
    }

    public function submit(): void {
        $this->verifyCsrf();
        if (!\Recaptcha::verify((string) $this->input('recaptcha_token', ''))) {
            $this->setFlash('error', 'Spam check failed. Please try again.');
            $this->redirect(BASE_URL . '/prayer');
        }
        $name = trim((string) $this->input('requester_name', ''));
        $subject = trim((string) $this->input('subject', ''));
        $text = trim((string) $this->input('request_text', ''));
        $email = trim((string) $this->input('email', ''));

        if ($name === '' || $subject === '' || strlen($text) < 10) {
            $this->setFlash('error', 'Name, subject, and prayer request are required.');
            $this->redirect(BASE_URL . '/prayer');
        }

        (new \App\Models\PrayerModel())->create([
            'requester_name' => $name,
            'email' => $email === '' ? null : strtolower($email),
            'phone' => $this->input('phone') ?: null,
            'subject' => $subject,
            'request_text' => $text,
            'category' => $this->input('category', 'others'),
            'is_private' => $this->input('is_private') ? 1 : 0,
        ]);

        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $siteName = \Settings::get('site_name', SITE_NAME);
            \Queue::email(
                strtolower($email),
                'We are praying with you',
                '<p>Dear ' . \Helpers::escape($name) . ',</p>'
                . '<p>Your prayer request &ldquo;' . \Helpers::escape($subject) . '&rdquo; has been received. '
                . 'Our prayer team will stand with you in agreement.</p>'
                . '<p>In His love,<br>' . \Helpers::escape($siteName) . '</p>'
            );
        }

        $this->setFlash('success', 'Your prayer request has been received.');
        $this->redirect(BASE_URL . '/prayer');
    }
}
