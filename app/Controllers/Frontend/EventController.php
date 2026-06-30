<?php

namespace App\Controllers\Frontend;

class EventController extends \Controller {
    private \App\Models\EventModel $events;
    private \App\Models\EventRegistrationModel $registrations;

    public function __construct() {
        parent::__construct();
        $this->events = new \App\Models\EventModel();
        $this->registrations = new \App\Models\EventRegistrationModel();
    }

    public function index(): void {
        $page = max(1, (int) $this->input('page', 1));
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $total = $this->events->publishedCount();

        $this->view('frontend.events.index', [
            'title' => 'Events',
            'description' => 'Upcoming events at ' . \Settings::get('site_name', SITE_NAME),
            'events' => $this->events->published($limit, $offset),
            'page' => $page,
            'totalPages' => max(1, (int) ceil($total / $limit)),
        ], 'public');
    }

    public function show(): void {
        $event = $this->events->findPublishedBySlug((string) ($_GET['slug'] ?? ''));
        if (!$event) {
            $this->notFound();
        }

        $this->view('frontend.events.single', [
            'title' => $event['title'],
            'description' => $event['short_description'] ?: $event['title'],
            'event' => $event,
            'registrationCount' => $this->registrations->countForEvent((int) $event['id']),
        ], 'public');
    }

    public function register(): void {
        $this->verifyCsrf();
        $event = $this->events->findPublishedBySlug((string) ($_GET['slug'] ?? ''));
        if (!$event) {
            $this->notFound();
        }

        if (empty($event['requires_registration'])) {
            $this->setFlash('error', 'This event is not open for registration.');
            $this->redirect(BASE_URL . '/events/' . $event['slug']);
        }

        $name = trim((string) $this->input('name', ''));
        $email = trim((string) $this->input('email', ''));
        $phone = trim((string) $this->input('phone', ''));
        $adults = max(1, (int) $this->input('adults', 1));
        $children = max(0, (int) $this->input('children', 0));

        if ($name === '' || ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $this->setFlash('error', 'Please provide a valid name and email address.');
            $this->redirect(BASE_URL . '/events/' . $event['slug']);
        }

        $limit = (int) ($event['registration_limit'] ?? 0);
        if ($limit > 0 && $this->registrations->countForEvent((int) $event['id']) >= $limit) {
            $this->setFlash('error', 'Registration for this event is already full.');
            $this->redirect(BASE_URL . '/events/' . $event['slug']);
        }

        $this->registrations->create([
            'event_id' => (int) $event['id'],
            'member_id' => $this->memberId,
            'name' => $name,
            'email' => $email !== '' ? $email : null,
            'phone' => $phone !== '' ? $phone : null,
            'adults' => $adults,
            'children' => $children,
            'status' => 'confirmed',
        ]);

        if ($email !== '') {
            $siteName = \Settings::get('site_name', SITE_NAME);
            $when = \Helpers::formatDate($event['event_date']);
            $venue = $event['venue'] ? ' at ' . \Helpers::escape($event['venue']) : '';
            \Queue::email(
                $email,
                'Registration confirmed: ' . $event['title'],
                '<p>Dear ' . \Helpers::escape($name) . ',</p>'
                . '<p>Your registration for <strong>' . \Helpers::escape($event['title']) . '</strong> on '
                . \Helpers::escape($when) . $venue . ' has been confirmed. We look forward to seeing you.</p>'
                . '<p>God bless you,<br>' . \Helpers::escape($siteName) . '</p>'
            );
        }

        $this->setFlash('success', 'Your event registration has been received.');
        $this->redirect(BASE_URL . '/events/' . $event['slug']);
    }

    private function notFound(): void {
        http_response_code(404);
        $this->view('frontend.404', ['title' => 'Event Not Found'], 'public');
        exit;
    }
}
