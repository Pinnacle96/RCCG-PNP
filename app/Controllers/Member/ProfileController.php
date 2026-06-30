<?php

namespace App\Controllers\Member;

class ProfileController extends \Controller {
    use LinksMember;

    private \App\Models\MemberModel $members;

    public function __construct() {
        parent::__construct();
        $this->members = new \App\Models\MemberModel();
    }

    public function show(): void {
        $this->requireAuth();

        $member = $this->linkedMember();
        $this->view('member.profile', [
            'title' => 'My Profile',
            'page_title' => 'My Profile',
            'member' => $member,
            'qr' => $member ? \Qr::dataUri((string) $member['member_code']) : '',
        ], 'member');
    }

    public function update(): void {
        $this->requireAuth();
        $this->verifyCsrf();

        $member = $this->linkedMember();
        if (!$member) {
            $this->setFlash('error', 'Your portal account is not linked to a member profile yet.');
            $this->redirect(BASE_URL . '/portal/profile');
        }

        $email = trim((string) $this->input('email', ''));
        $data = [
            'phone' => $this->nullableString('phone'),
            'alt_phone' => $this->nullableString('alt_phone'),
            'email' => $email === '' ? null : strtolower($email),
            'address' => $this->nullableString('address'),
            'occupation' => $this->nullableString('occupation'),
            'emergency_contact' => $this->nullableString('emergency_contact'),
            'emergency_phone' => $this->nullableString('emergency_phone'),
        ];

        if ($data['email'] !== null && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Email must be valid.');
            $this->redirect(BASE_URL . '/portal/profile');
        }

        if (!empty($_FILES['profile_photo']['name'])) {
            $uploader = new \Uploader(UPLOAD_PATH, ALLOWED_IMAGE_TYPES, MAX_IMAGE_SIZE);
            $stored = $uploader->upload('profile_photo', 'members/');
            if ($stored === null) {
                $this->setFlash('error', 'Profile photo: ' . implode(' ', $uploader->getErrors()));
                $this->redirect(BASE_URL . '/portal/profile');
            }
            $data['profile_photo'] = $stored;
        }

        $this->members->update((int) $member['id'], $data);
        $this->setFlash('success', 'Profile updated successfully.');
        $this->redirect(BASE_URL . '/portal/profile');
    }

    private function nullableString(string $key): ?string {
        $value = trim((string) $this->input($key, ''));
        return $value === '' ? null : $value;
    }
}
