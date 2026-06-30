<?php

namespace App\Models;

class BlogModel extends \Model {
    protected string $table = 'blog_posts';

    public function latestPublished(int $limit = 3): array {
        return \Database::fetchAll(
            'SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY COALESCE(published_at, created_at) DESC, id DESC LIMIT ' . (int) $limit
        );
    }

    public function published(int $limit = ITEMS_PER_PAGE, int $offset = 0): array {
        return \Database::fetchAll(
            'SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY COALESCE(published_at, created_at) DESC, id DESC LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset
        );
    }

    public function publishedCount(): int {
        return (int) \Database::fetchColumn('SELECT COUNT(*) FROM blog_posts WHERE is_published = 1');
    }

    public function findPublishedBySlug(string $slug): ?array {
        return \Database::fetchOne('SELECT * FROM blog_posts WHERE slug = ? AND is_published = 1 LIMIT 1', [$slug]);
    }
}
