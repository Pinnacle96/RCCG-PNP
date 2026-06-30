<?php

namespace App\Controllers\Frontend;

class SearchController extends \Controller {
    public function index(): void {
        $q = trim((string) $this->input('q', ''));
        $results = ['sermons' => [], 'events' => [], 'ministries' => [], 'blog' => []];
        if ($q !== '') {
            $term = '%' . $q . '%';
            $results['sermons'] = \Database::fetchAll('SELECT title, slug, preacher FROM sermons WHERE is_published = 1 AND (title LIKE ? OR preacher LIKE ? OR tags LIKE ?) LIMIT 10', [$term, $term, $term]);
            $results['events'] = \Database::fetchAll('SELECT title, slug, event_date FROM events WHERE is_published = 1 AND title LIKE ? LIMIT 10', [$term]);
            $results['ministries'] = \Database::fetchAll('SELECT name AS title, slug, leader_name FROM ministries WHERE is_active = 1 AND name LIKE ? LIMIT 10', [$term]);
            $results['blog'] = \Database::fetchAll('SELECT title, slug, excerpt FROM blog_posts WHERE is_published = 1 AND (title LIKE ? OR excerpt LIKE ? OR tags LIKE ?) LIMIT 10', [$term, $term, $term]);
        }
        $this->view('frontend.search', ['title' => 'Search', 'q' => $q, 'results' => $results], 'public');
    }
}
