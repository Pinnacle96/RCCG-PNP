<?php

namespace App\Models;

class GalleryModel extends \Model {
    protected string $table = 'gallery_albums';

    public function albums(): array {
        return \Database::fetchAll('SELECT * FROM gallery_albums WHERE is_published = 1 ORDER BY COALESCE(event_date, created_at) DESC, id DESC');
    }

    public function findAlbumBySlug(string $slug): ?array {
        return \Database::fetchOne('SELECT * FROM gallery_albums WHERE slug = ? AND is_published = 1 LIMIT 1', [$slug]);
    }

    public function photos(int $albumId): array {
        return \Database::fetchAll('SELECT * FROM gallery WHERE album_id = ? ORDER BY sort_order ASC, id ASC', [$albumId]);
    }
}
