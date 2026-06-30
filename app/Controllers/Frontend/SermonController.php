<?php

namespace App\Controllers\Frontend;

class SermonController extends \Controller {
    private \App\Models\SermonModel $sermons;
    private \App\Models\SermonSeriesModel $seriesModel;

    public function __construct() {
        parent::__construct();
        $this->sermons = new \App\Models\SermonModel();
        $this->seriesModel = new \App\Models\SermonSeriesModel();
    }

    public function index(): void {
        $page = max(1, (int) $this->input('page', 1));
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $total = $this->sermons->publishedCount();

        $this->view('frontend.sermons.index', [
            'title' => 'Sermons',
            'description' => 'Listen to sermons from ' . \Settings::get('site_name', SITE_NAME),
            'sermons' => $this->sermons->published($limit, $offset),
            'seriesList' => $this->seriesModel->active(),
            'page' => $page,
            'totalPages' => max(1, (int) ceil($total / $limit)),
        ], 'public');
    }

    public function show(): void {
        $sermon = $this->sermons->findPublishedBySlug((string) ($_GET['slug'] ?? ''));
        if (!$sermon) {
            $this->notFound();
        }

        $this->view('frontend.sermons.single', [
            'title' => $sermon['title'],
            'description' => $sermon['description'] ?: $sermon['title'],
            'sermon' => $sermon,
        ], 'public');
    }

    public function series(): void {
        $series = $this->seriesModel->findActiveBySlug((string) ($_GET['slug'] ?? ''));
        if (!$series) {
            $this->notFound();
        }

        $this->view('frontend.sermons.series', [
            'title' => $series['title'],
            'description' => $series['description'] ?: ('Messages in the ' . $series['title'] . ' series'),
            'series' => $series,
            'sermons' => $this->sermons->publishedBySeries((int) $series['id']),
        ], 'public');
    }

    private function notFound(): void {
        http_response_code(404);
        $this->view('frontend.404', ['title' => 'Sermon Not Found'], 'public');
        exit;
    }
}
