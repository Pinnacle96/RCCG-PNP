<?php

namespace App\Models;

class MinistryModel extends \Model {
    protected string $table = 'ministries';

    public function active(int $limit = 6): array {
        return \Database::fetchAll(
            'SELECT * FROM ministries WHERE is_active = 1 ORDER BY display_order ASC, name ASC LIMIT ' . (int) $limit
        );
    }

    public function activeAll(): array {
        return \Database::fetchAll('SELECT * FROM ministries WHERE is_active = 1 ORDER BY display_order ASC, name ASC');
    }

    public function findActiveBySlug(string $slug): ?array {
        return \Database::fetchOne('SELECT * FROM ministries WHERE slug = ? AND is_active = 1 LIMIT 1', [$slug]);
    }
}
