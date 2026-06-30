<?php

namespace App\Models;

class PrayerModel extends \Model {
    protected string $table = 'prayer_requests';

    public function publicRequests(int $limit = 12): array {
        return \Database::fetchAll(
            "SELECT * FROM prayer_requests WHERE is_private = 0 AND status IN ('new','praying','answered') ORDER BY created_at DESC LIMIT " . (int) $limit
        );
    }

    /**
     * Paginated admin list with optional status/category/search filters.
     */
    public function paginate(array $filters, int $limit, int $offset): array {
        [$where, $params] = $this->filterClause($filters);
        return \Database::fetchAll(
            "SELECT pr.*, CONCAT(u.email) AS assignee_email
             FROM prayer_requests pr
             LEFT JOIN users u ON u.id = pr.assigned_to
             {$where}
             ORDER BY FIELD(pr.status,'new','praying','answered','archived'), pr.created_at DESC
             LIMIT {$limit} OFFSET {$offset}",
            $params
        );
    }

    public function totalFiltered(array $filters): int {
        [$where, $params] = $this->filterClause($filters);
        return (int) \Database::fetchColumn("SELECT COUNT(*) FROM prayer_requests pr {$where}", $params);
    }

    public function statusCounts(): array {
        $rows = \Database::fetchAll('SELECT status, COUNT(*) total FROM prayer_requests GROUP BY status');
        $counts = ['new' => 0, 'praying' => 0, 'answered' => 0, 'archived' => 0];
        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }
        return $counts;
    }

    private function filterClause(array $filters): array {
        $clauses = [];
        $params = [];

        $status = $filters['status'] ?? '';
        if (in_array($status, ['new', 'praying', 'answered', 'archived'], true)) {
            $clauses[] = 'pr.status = ?';
            $params[] = $status;
        }

        $category = $filters['category'] ?? '';
        if ($category !== '') {
            $clauses[] = 'pr.category = ?';
            $params[] = $category;
        }

        $search = $filters['q'] ?? '';
        if ($search !== '') {
            $clauses[] = '(pr.requester_name LIKE ? OR pr.subject LIKE ? OR pr.request_text LIKE ?)';
            $term = '%' . $search . '%';
            array_push($params, $term, $term, $term);
        }

        $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';
        return [$where, $params];
    }
}
