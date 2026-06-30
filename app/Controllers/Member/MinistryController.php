<?php

namespace App\Controllers\Member;

class MinistryController extends \Controller {
    use LinksMember;

    /** GET /portal/ministry — ministries the member is enrolled in. */
    public function index(): void {
        $this->requireAuth();
        $member = $this->requireLinkedMember('My Ministry');
        $memberId = (int) $member['id'];

        // Ministries via the membership join table, plus the member's primary ministry.
        $ministries = \Database::fetchAll(
            "SELECT m.*, mm.role AS member_role, mm.joined_date
             FROM ministries m
             INNER JOIN ministry_members mm ON mm.ministry_id = m.id
             WHERE mm.member_id = ? AND mm.is_active = 1 AND m.is_active = 1
             ORDER BY m.display_order ASC, m.name ASC",
            [$memberId]
        );

        $joinedIds = array_column($ministries, 'id');
        if (!empty($member['ministry_id']) && !in_array((int) $member['ministry_id'], array_map('intval', $joinedIds), true)) {
            $primary = \Database::fetchOne('SELECT * FROM ministries WHERE id = ? AND is_active = 1 LIMIT 1', [(int) $member['ministry_id']]);
            if ($primary) {
                $primary['member_role'] = null;
                $primary['joined_date'] = null;
                array_unshift($ministries, $primary);
            }
        }

        $this->view('member.ministry', [
            'title' => 'My Ministry',
            'page_title' => 'My Ministry',
            'ministries' => $ministries,
        ], 'member');
    }
}
