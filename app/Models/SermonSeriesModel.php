<?php

namespace App\Models;

class SermonSeriesModel extends \Model {
    protected string $table = 'sermon_series';

    public function active(): array {
        return \Database::fetchAll('SELECT * FROM sermon_series WHERE is_active = 1 ORDER BY start_date DESC, id DESC');
    }

    public function findActiveBySlug(string $slug): ?array {
        return \Database::fetchOne('SELECT * FROM sermon_series WHERE slug = ? AND is_active = 1 LIMIT 1', [$slug]);
    }
}
