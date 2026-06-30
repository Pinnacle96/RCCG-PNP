<?php

namespace App\Models;

class EventModel extends \Model {
    protected string $table = 'events';

    public function upcoming(int $limit = 3): array {
        return \Database::fetchAll(
            'SELECT * FROM events WHERE is_published = 1 AND event_date >= CURDATE() ORDER BY event_date ASC, id ASC LIMIT ' . (int) $limit
        );
    }

    public function published(int $limit = ITEMS_PER_PAGE, int $offset = 0): array {
        return \Database::fetchAll(
            'SELECT * FROM events WHERE is_published = 1 ORDER BY event_date ASC, id ASC LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset
        );
    }

    public function publishedCount(): int {
        return (int) \Database::fetchColumn('SELECT COUNT(*) FROM events WHERE is_published = 1');
    }

    public function findPublishedBySlug(string $slug): ?array {
        return \Database::fetchOne('SELECT * FROM events WHERE slug = ? AND is_published = 1 LIMIT 1', [$slug]);
    }
}
