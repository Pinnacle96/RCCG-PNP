<?php

namespace App\Models;

class MemberModel extends \Model {
    protected string $table = 'members';

    public function activeCount(): int {
        return $this->count(['membership_status' => 'active']);
    }

    public function paginate(string $search = '', int $limit = ADMIN_ITEMS_PER_PAGE, int $offset = 0): array {
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = " WHERE member_code LIKE :search_code
                OR first_name LIKE :search_first
                OR last_name LIKE :search_last
                OR email LIKE :search_email
                OR phone LIKE :search_phone";
            $params = $this->searchParams($search);
        }

        return \Database::fetchAll(
            "SELECT * FROM members{$where} ORDER BY created_at DESC, id DESC LIMIT {$limit} OFFSET {$offset}",
            $params
        );
    }

    public function total(string $search = ''): int {
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = " WHERE member_code LIKE :search_code
                OR first_name LIKE :search_first
                OR last_name LIKE :search_last
                OR email LIKE :search_email
                OR phone LIKE :search_phone";
            $params = $this->searchParams($search);
        }

        return (int) \Database::fetchColumn("SELECT COUNT(*) FROM members{$where}", $params);
    }

    public function generateMemberCode(): string {
        $next = ((int) \Database::fetchColumn('SELECT COALESCE(MAX(id), 0) + 1 FROM members')) ?: 1;

        do {
            $code = 'RPP' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            $next++;
        } while ($this->findBy('member_code', $code));

        return $code;
    }

    public function statusCounts(): array {
        $rows = \Database::fetchAll('SELECT membership_status, COUNT(*) total FROM members GROUP BY membership_status');
        $counts = [
            'active' => 0,
            'inactive' => 0,
            'transferred' => 0,
            'deceased' => 0,
        ];

        foreach ($rows as $row) {
            $counts[$row['membership_status']] = (int) $row['total'];
        }

        return $counts;
    }

    private function searchParams(string $search): array {
        $term = '%' . $search . '%';
        return [
            ':search_code' => $term,
            ':search_first' => $term,
            ':search_last' => $term,
            ':search_email' => $term,
            ':search_phone' => $term,
        ];
    }
}
