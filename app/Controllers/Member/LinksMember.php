<?php

namespace App\Controllers\Member;

/**
 * Shared helper for member-area controllers: resolves the member record
 * linked to the authenticated portal user, self-healing the link by email
 * when the user has no member_id yet (SSOT §8).
 */
trait LinksMember {
    protected function linkedMember(): ?array {
        $user = \Auth::user();
        if (!$user) {
            return null;
        }

        $members = new \App\Models\MemberModel();
        if (!empty($user['member_id'])) {
            return $members->find((int) $user['member_id']);
        }

        $member = $members->findBy('email', $user['email']);
        if ($member) {
            (new \App\Models\UserModel())->update((int) $user['id'], ['member_id' => $member['id']]);
            unset($_SESSION['user']);
            return $member;
        }

        return null;
    }

    /** Render a "link your profile" notice when no member record is attached. */
    protected function requireLinkedMember(string $page): ?array {
        $member = $this->linkedMember();
        if (!$member) {
            $this->view('member.placeholder', [
                'title' => $page,
                'page_title' => $page,
                'unlinked' => true,
            ], 'member');
            exit;
        }
        return $member;
    }
}
