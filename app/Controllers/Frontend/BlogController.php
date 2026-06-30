<?php

namespace App\Controllers\Frontend;

class BlogController extends \Controller {
    private \App\Models\BlogModel $posts;

    public function __construct() {
        parent::__construct();
        $this->posts = new \App\Models\BlogModel();
    }

    public function index(): void {
        $page = max(1, (int) $this->input('page', 1));
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $total = $this->posts->publishedCount();
        $this->view('frontend.blog.index', [
            'title' => 'Blog',
            'posts' => $this->posts->published($limit, $offset),
            'page' => $page,
            'totalPages' => max(1, (int) ceil($total / $limit)),
        ], 'public');
    }

    public function show(): void {
        $post = $this->posts->findPublishedBySlug((string) ($_GET['slug'] ?? ''));
        if (!$post) {
            http_response_code(404);
            $this->view('frontend.404', ['title' => 'Post Not Found'], 'public');
            exit;
        }
        $this->view('frontend.blog.single', ['title' => $post['title'], 'post' => $post], 'public');
    }
}
