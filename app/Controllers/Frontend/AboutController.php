<?php

namespace App\Controllers\Frontend;

class AboutController extends \Controller {
    public function index(): void {
        $this->view('frontend.about', [
            'title' => 'About Us',
            'description' => 'Learn about ' . \Settings::get('site_name', SITE_NAME),
        ], 'public');
    }
}
