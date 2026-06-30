<?php

namespace App\Models;

class SermonModel extends \Model {
    protected string $table = 'sermons';

    public function latestPublished(int $limit = 3): array {
        return \Database::fetchAll(
            'SELECT sermons.*, sermon_series.title AS series_title, sermon_series.slug AS series_slug FROM sermons LEFT JOIN sermon_series ON sermon_series.id = sermons.series_id WHERE sermons.is_published = 1 ORDER BY sermon_date DESC, sermons.id DESC LIMIT ' . (int) $limit
        );
    }

    public function published(int $limit = ITEMS_PER_PAGE, int $offset = 0): array {
        return \Database::fetchAll(
            'SELECT sermons.*, sermon_series.title AS series_title, sermon_series.slug AS series_slug FROM sermons LEFT JOIN sermon_series ON sermon_series.id = sermons.series_id WHERE sermons.is_published = 1 ORDER BY sermon_date DESC, sermons.id DESC LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset
        );
    }

    public function publishedCount(): int {
        return (int) \Database::fetchColumn('SELECT COUNT(*) FROM sermons WHERE is_published = 1');
    }

    public function findPublishedBySlug(string $slug): ?array {
        return \Database::fetchOne('SELECT sermons.*, sermon_series.title AS series_title, sermon_series.slug AS series_slug FROM sermons LEFT JOIN sermon_series ON sermon_series.id = sermons.series_id WHERE sermons.slug = ? AND sermons.is_published = 1 LIMIT 1', [$slug]);
    }

    public function publishedBySeries(int $seriesId): array {
        return \Database::fetchAll(
            'SELECT sermons.*, sermon_series.title AS series_title, sermon_series.slug AS series_slug FROM sermons LEFT JOIN sermon_series ON sermon_series.id = sermons.series_id WHERE sermons.series_id = ? AND sermons.is_published = 1 ORDER BY sermon_date DESC, sermons.id DESC',
            [$seriesId]
        );
    }
}
