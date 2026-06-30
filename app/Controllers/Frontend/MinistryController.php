<?php

namespace App\Controllers\Frontend;

class MinistryController extends \Controller {
    private \App\Models\MinistryModel $ministries;

    public function __construct() {
        parent::__construct();
        $this->ministries = new \App\Models\MinistryModel();
    }

    public function index(): void {
        $this->view('frontend.ministries.index', [
            'title' => 'Ministries',
            'description' => 'Ministries at ' . \Settings::get('site_name', SITE_NAME),
            'ministries' => $this->ministries->activeAll(),
        ], 'public');
    }

    public function show(): void {
        $ministry = $this->ministries->findActiveBySlug((string) ($_GET['slug'] ?? ''));
        if (!$ministry) {
            $this->notFound();
        }

        $this->view('frontend.ministries.single', [
            'title' => $ministry['name'],
            'description' => $ministry['short_desc'] ?: $ministry['name'],
            'ministry' => $ministry,
        ], 'public');
    }

    private function notFound(): void {
        http_response_code(404);
        $this->view('frontend.404', ['title' => 'Ministry Not Found'], 'public');
        exit;
    }
}
