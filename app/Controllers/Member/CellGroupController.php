<?php

namespace App\Controllers\Member;

class CellGroupController extends \Controller {
    use LinksMember;

    /** GET /portal/cellgroup — the member's cell group, leaders and fellow members. */
    public function index(): void {
        $this->requireAuth();
        $member = $this->requireLinkedMember('My Cell Group');

        $group = null;
        $leaders = [];
        $fellows = [];

        if (!empty($member['cell_group_id'])) {
            $group = \Database::fetchOne('SELECT * FROM cell_groups WHERE id = ? AND is_active = 1 LIMIT 1', [(int) $member['cell_group_id']]);
        }

        if ($group) {
            $leaders = [
                'leader' => $this->memberName($group['leader_id'] ?? null),
                'co_leader' => $this->memberName($group['co_leader_id'] ?? null),
            ];
            $fellows = \Database::fetchAll(
                "SELECT id, member_code, first_name, last_name, phone, profile_photo
                 FROM members
                 WHERE cell_group_id = ? AND id != ? AND membership_status = 'active'
                 ORDER BY first_name ASC, last_name ASC",
                [(int) $group['id'], (int) $member['id']]
            );
        }

        $this->view('member.cellgroup', [
            'title' => 'My Cell Group',
            'page_title' => 'My Cell Group',
            'group' => $group,
            'leaders' => $leaders,
            'fellows' => $fellows,
        ], 'member');
    }

    private function memberName($id): ?string {
        if (empty($id)) {
            return null;
        }
        $row = \Database::fetchOne('SELECT first_name, last_name FROM members WHERE id = ? LIMIT 1', [(int) $id]);
        return $row ? trim($row['first_name'] . ' ' . $row['last_name']) : null;
    }
}
