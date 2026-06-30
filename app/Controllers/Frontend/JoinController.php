<?php

namespace App\Controllers\Frontend;

class JoinController extends \Controller {
    public function index(): void {
        $this->view('frontend.join', ['title' => 'Join Us'], 'public');
    }

    /** POST /join — membership application. Creates a pending (inactive) member record. */
    public function submit(): void {
        $this->verifyCsrf();
        if (!\Recaptcha::verify((string) $this->input('recaptcha_token', ''))) {
            $this->setFlash('error', 'Spam check failed. Please try again.');
            $this->redirect(BASE_URL . '/join');
        }
        $members = new \App\Models\MemberModel();

        $firstName = trim((string) $this->input('first_name', ''));
        $lastName = trim((string) $this->input('last_name', ''));
        $gender = (string) $this->input('gender', '');
        $email = strtolower(trim((string) $this->input('email', '')));

        $errors = [];
        if ($firstName === '') { $errors[] = 'First name is required.'; }
        if ($lastName === '') { $errors[] = 'Last name is required.'; }
        if (!in_array($gender, ['male', 'female'], true)) { $errors[] = 'Please select your gender.'; }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Please enter a valid email address.'; }
        if ($email !== '' && $members->findBy('email', $email)) { $errors[] = 'A member with that email already exists.'; }
        if ($errors) {
            $this->setFlash('error', implode(' ', $errors));
            $this->redirect(BASE_URL . '/join');
        }

        $interest = trim((string) $this->input('ministry_interest', ''));
        $data = [
            'member_code' => $members->generateMemberCode(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $this->nullable('middle_name'),
            'gender' => $gender,
            'date_of_birth' => $this->nullableDate('date_of_birth'),
            'phone' => $this->nullable('phone'),
            'email' => $email === '' ? null : $email,
            'address' => $this->nullable('address'),
            'state_of_origin' => $this->nullable('state_of_origin'),
            'occupation' => $this->nullable('occupation'),
            'marital_status' => in_array($this->input('marital_status'), ['single', 'married', 'divorced', 'widowed'], true) ? $this->input('marital_status') : null,
            'membership_type' => 'full',
            'membership_status' => 'inactive', // pending review by the church office
            'join_date' => date('Y-m-d'),
            'water_baptized' => $this->input('water_baptized') ? 1 : 0,
            'holy_ghost_baptized' => $this->input('holy_ghost_baptized') ? 1 : 0,
            'notes' => $interest !== '' ? 'Ministry interest: ' . $interest : null,
        ];

        if (!empty($_FILES['profile_photo']['name'])) {
            $uploader = new \Uploader(UPLOAD_PATH, ALLOWED_IMAGE_TYPES, MAX_IMAGE_SIZE);
            $stored = $uploader->upload('profile_photo', 'members/');
            if ($stored === null) {
                $this->setFlash('error', 'Passport photo: ' . implode(' ', $uploader->getErrors()));
                $this->redirect(BASE_URL . '/join');
            }
            $data['profile_photo'] = $stored;
        }

        $members->create($data);

        $siteName = \Settings::get('site_name', SITE_NAME);
        if ($email !== '') {
            \Queue::email(
                $email,
                'Welcome to ' . $siteName,
                '<p>Dear ' . \Helpers::escape($firstName) . ',</p>'
                . '<p>Thank you for your interest in becoming a member of ' . \Helpers::escape($siteName) . '. '
                . 'We have received your application and our membership team will be in touch shortly.</p>'
                . '<p>God bless you,<br>' . \Helpers::escape($siteName) . '</p>'
            );
        }

        $this->setFlash('success', 'Thank you! Your membership application has been received. Our team will contact you soon.');
        $this->redirect(BASE_URL . '/join');
    }

    private function nullable(string $key): ?string {
        $value = trim((string) $this->input($key, ''));
        return $value === '' ? null : $value;
    }

    private function nullableDate(string $key): ?string {
        $value = trim((string) $this->input($key, ''));
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
    }
}
