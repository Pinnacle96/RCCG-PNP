<?php

namespace App\Controllers\Frontend;

class GalleryController extends \Controller {
    private \App\Models\GalleryModel $gallery;

    public function __construct() {
        parent::__construct();
        $this->gallery = new \App\Models\GalleryModel();
    }

    public function index(): void {
        $this->view('frontend.gallery.index', [
            'title' => 'Gallery',
            'albums' => $this->gallery->albums(),
        ], 'public');
    }

    public function album(): void {
        $album = $this->gallery->findAlbumBySlug((string) ($_GET['slug'] ?? ''));
        if (!$album) {
            http_response_code(404);
            $this->view('frontend.404', ['title' => 'Album Not Found'], 'public');
            exit;
        }
        $this->view('frontend.gallery.album', [
            'title' => $album['title'],
            'album' => $album,
            'photos' => $this->gallery->photos((int) $album['id']),
        ], 'public');
    }
}
