<?php

namespace App\Controllers\Frontend;

class HomeController extends \Controller {
    public function index(): void {
        $this->view('frontend.home', [
            'title' => \Settings::get('site_name', SITE_NAME),
            'description' => \Settings::get('site_tagline', SITE_TAGLINE),
            'latestSermons' => (new \App\Models\SermonModel())->latestPublished(3),
            'upcomingEvents' => (new \App\Models\EventModel())->upcoming(3),
            'ministries' => (new \App\Models\MinistryModel())->active(3),
        ], 'public');
    }

    public function livestream(): void {
        $url = (string) \Settings::get('livestream_url', '');
        $channelId = (string) \Settings::get('livestream_channel_id', '');

        // Weekly service schedule (ISO day-of-week => [hour, minute, label]) used
        // to compute the next service for the countdown timer.
        $schedule = [
            7 => [
                [8, 0, \Settings::get('service_sunday_first', SERVICE_SUNDAY_FIRST)],
            ],
            3 => [[18, 0, \Settings::get('service_wednesday', SERVICE_WEDNESDAY)]],
            5 => [[22, 0, \Settings::get('service_friday', SERVICE_FRIDAY)]],
        ];

        $this->view('frontend.livestream', [
            'title' => 'Live Stream',
            'description' => 'Watch ' . \Settings::get('site_name', SITE_NAME) . ' live online.',
            'isLive' => (string) \Settings::get('livestream_is_live', '0') === '1',
            'embedUrl' => $this->youtubeEmbedUrl($url, $channelId),
            'offlineMessage' => \Settings::get('livestream_offline_message', 'We are currently offline. Join us live at our next service.'),
            'nextService' => $this->nextService($schedule),
            'serviceTimes' => array_values(array_filter([
                \Settings::get('service_sunday_first', SERVICE_SUNDAY_FIRST),
                \Settings::get('service_sunday_second', SERVICE_SUNDAY_SECOND),
                \Settings::get('service_wednesday', SERVICE_WEDNESDAY),
                \Settings::get('service_friday', SERVICE_FRIDAY),
            ])),
        ], 'public');
    }

    /** Build a YouTube embed URL from an explicit stream URL or a channel ID. */
    private function youtubeEmbedUrl(string $url, string $channelId): string {
        $url = trim($url);
        if ($url !== '') {
            if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|live/|embed/|v/))([\w-]{11})~', $url, $m)) {
                return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0';
            }
            return $url; // Already an embed/live URL — pass through.
        }
        $channelId = trim($channelId);
        if ($channelId !== '') {
            return 'https://www.youtube.com/embed/live_stream?channel=' . rawurlencode($channelId);
        }
        return '';
    }

    /** Find the soonest upcoming service from a weekly schedule. */
    private function nextService(array $schedule): array {
        $now = new \DateTime('now');
        $best = null;
        for ($offset = 0; $offset <= 7; $offset++) {
            $day = (clone $now)->modify('+' . $offset . ' day');
            $dow = (int) $day->format('N');
            foreach ($schedule[$dow] ?? [] as [$hour, $minute, $label]) {
                $candidate = (clone $day)->setTime($hour, $minute, 0);
                if ($candidate > $now && ($best === null || $candidate < $best['dt'])) {
                    $best = ['dt' => $candidate, 'label' => $label];
                }
            }
        }
        if ($best === null) {
            return ['label' => '', 'iso' => '', 'human' => ''];
        }
        return [
            'label' => (string) $best['label'],
            'iso' => $best['dt']->format('c'),
            'human' => $best['dt']->format('l, M j \a\t g:i A'),
        ];
    }

    public function page(): void {
        $page = $_GET['page'] ?? 'page';
        $titles = [
            'about' => 'About Us',
            'events' => 'Events',
            'ministries' => 'Ministries',
            'give' => 'Give',
            'prayer' => 'Prayer Requests',
            'blog' => 'Blog',
            'gallery' => 'Gallery',
            'contact' => 'Contact',
            'livestream' => 'Live Stream',
            'join' => 'Membership Application',
            'search' => 'Search',
        ];

        $this->view('frontend.placeholder', [
            'title' => $titles[$page] ?? ucwords(str_replace('-', ' ', $page)),
            'pageKey' => $page,
        ], 'public');
    }

    public function notFound(): void {
        http_response_code(404);
        $this->view('frontend.404', ['title' => 'Page Not Found'], 'public');
    }
}
