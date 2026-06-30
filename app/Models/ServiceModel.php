<?php

namespace App\Models;

class ServiceModel extends \Model {
    protected string $table = 'services';

    public function paginate(string $search = '', int $limit = ADMIN_ITEMS_PER_PAGE, int $offset = 0): array {
        $where = '';
        $params = [];
        if ($search !== '') {
            $where = 'WHERE service_type LIKE :search OR theme LIKE :search OR preacher LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        return \Database::fetchAll(
            "SELECT services.*, COUNT(attendance.id) AS marked_count
             FROM services
             LEFT JOIN attendance ON attendance.service_id = services.id
             {$where}
             GROUP BY services.id
             ORDER BY service_date DESC, services.id DESC
             LIMIT {$limit} OFFSET {$offset}",
            $params
        );
    }

    public function total(string $search = ''): int {
        if ($search === '') {
            return (int) \Database::fetchColumn('SELECT COUNT(*) FROM services');
        }

        return (int) \Database::fetchColumn(
            'SELECT COUNT(*) FROM services WHERE service_type LIKE :search OR theme LIKE :search OR preacher LIKE :search',
            [':search' => '%' . $search . '%']
        );
    }

    public function findWithAttendanceStats(int $id): ?array {
        return \Database::fetchOne(
            "SELECT services.*, COUNT(attendance.id) AS marked_count
             FROM services
             LEFT JOIN attendance ON attendance.service_id = services.id
             WHERE services.id = ?
             GROUP BY services.id
             LIMIT 1",
            [$id]
        );
    }
}
