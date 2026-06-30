<?php

namespace App\Controllers\Api;

/**
 * Public AJAX endpoints (SSOT §10 — Public API).
 * No authentication required; state-changing calls are CSRF-protected.
 */
class PublicController extends ApiController {

    /* ------------------------------------------------------------------ */
    /* Sermons                                                             */
    /* ------------------------------------------------------------------ */

    /** GET /api/sermons/search?q= — live sermon search. */
    public function sermonSearch(): void {
        $q = (string) $this->param('q', '');
        if ($q === '') {
            $this->ok(['results' => []]);
        }
        $term = '%' . $q . '%';
        $rows = \Database::fetchAll(
            'SELECT title, slug, preacher, sermon_date, thumbnail
             FROM sermons
             WHERE is_published = 1 AND (title LIKE ? OR preacher LIKE ? OR scripture_ref LIKE ? OR tags LIKE ?)
             ORDER BY sermon_date DESC LIMIT 10',
            [$term, $term, $term, $term]
        );
        $this->ok(['results' => $rows], count($rows) . ' result(s)');
    }

    /** GET /api/sermons/filter — filter sermons by params. */
    public function sermonFilter(): void {
        $where = ['is_published = 1'];
        $params = [];

        $preacher = (string) $this->param('preacher', '');
        if ($preacher !== '') { $where[] = 'preacher = ?'; $params[] = $preacher; }

        $series = (int) $this->param('series_id', 0);
        if ($series > 0) { $where[] = 'series_id = ?'; $params[] = $series; }

        $type = (string) $this->param('type', '');
        if (in_array($type, ['sunday', 'midweek', 'special', 'program'], true)) {
            $where[] = 'sermon_type = ?';
            $params[] = $type;
        }

        $from = (string) $this->param('from', '');
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) { $where[] = 'sermon_date >= ?'; $params[] = $from; }
        $to = (string) $this->param('to', '');
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) { $where[] = 'sermon_date <= ?'; $params[] = $to; }

        $sort = (string) $this->param('sort', 'newest');
        $order = match ($sort) {
            'oldest' => 'sermon_date ASC',
            'views'  => 'views DESC',
            'downloads' => 'downloads DESC',
            default  => 'sermon_date DESC',
        };

        $page = max(1, (int) $this->param('page', 1));
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $whereSql = implode(' AND ', $where);
        $rows = \Database::fetchAll(
            "SELECT id, title, slug, preacher, scripture_ref, sermon_date, sermon_type, duration_mins, thumbnail, views, downloads
             FROM sermons WHERE {$whereSql} ORDER BY {$order} LIMIT {$limit} OFFSET {$offset}",
            $params
        );
        $total = (int) \Database::fetchColumn("SELECT COUNT(*) FROM sermons WHERE {$whereSql}", $params);

        $this->ok([
            'results' => $rows,
            'total' => $total,
            'page' => $page,
            'totalPages' => max(1, (int) ceil($total / $limit)),
        ]);
    }

    /** POST /api/sermons/view — increment view count. */
    public function sermonView(): void {
        $this->requirePost();
        $slug = (string) $this->param('slug', '');
        $id = (int) $this->param('id', 0);
        $sermon = $id > 0
            ? \Database::fetchOne('SELECT id FROM sermons WHERE id = ? AND is_published = 1', [$id])
            : \Database::fetchOne('SELECT id FROM sermons WHERE slug = ? AND is_published = 1', [$slug]);
        if (!$sermon) {
            $this->fail('Sermon not found.', [], 404);
        }
        \Database::execute('UPDATE sermons SET views = views + 1 WHERE id = ?', [$sermon['id']]);
        $views = (int) \Database::fetchColumn('SELECT views FROM sermons WHERE id = ?', [$sermon['id']]);
        $this->ok(['views' => $views]);
    }

    /** POST /api/sermons/download — increment download count and return file URL. */
    public function sermonDownload(): void {
        $this->requirePost();
        $slug = (string) $this->param('slug', '');
        $id = (int) $this->param('id', 0);
        $sermon = $id > 0
            ? \Database::fetchOne('SELECT id, audio_file FROM sermons WHERE id = ? AND is_published = 1', [$id])
            : \Database::fetchOne('SELECT id, audio_file FROM sermons WHERE slug = ? AND is_published = 1', [$slug]);
        if (!$sermon) {
            $this->fail('Sermon not found.', [], 404);
        }
        if (empty($sermon['audio_file'])) {
            $this->fail('No audio file is available for this sermon.', [], 404);
        }
        \Database::execute('UPDATE sermons SET downloads = downloads + 1 WHERE id = ?', [$sermon['id']]);
        $url = str_starts_with($sermon['audio_file'], 'http')
            ? $sermon['audio_file']
            : UPLOAD_URL . ltrim(str_replace('uploads/', '', $sermon['audio_file']), '/');
        $this->ok(['url' => $url]);
    }

    /* ------------------------------------------------------------------ */
    /* Prayer                                                              */
    /* ------------------------------------------------------------------ */

    /** POST /api/prayer/submit — submit a prayer request. */
    public function prayerSubmit(): void {
        $this->requirePost();
        $this->guardCsrf();
        if (!\Recaptcha::verify((string) $this->param('recaptcha_token', ''))) {
            $this->fail('Spam check failed. Please try again.', [], 422);
        }

        $name = (string) $this->param('requester_name', '');
        $subject = (string) $this->param('subject', '');
        $text = (string) $this->param('request_text', '');
        $email = (string) $this->param('email', '');
        $errors = [];
        if ($name === '') { $errors['requester_name'] = 'Your name is required.'; }
        if ($subject === '') { $errors['subject'] = 'A subject is required.'; }
        if (strlen($text) < 10) { $errors['request_text'] = 'Please describe your request (min 10 characters).'; }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Enter a valid email address.'; }
        if ($errors) {
            $this->fail('Please correct the errors below.', $errors, 422);
        }

        $category = (string) $this->param('category', 'others');
        $allowed = ['healing', 'deliverance', 'finance', 'family', 'salvation', 'career', 'marriage', 'thanksgiving', 'others'];

        \Database::insert('prayer_requests', [
            'requester_name' => $name,
            'email' => $email === '' ? null : strtolower($email),
            'phone' => ($this->param('phone') ?: null),
            'subject' => $subject,
            'request_text' => $text,
            'category' => in_array($category, $allowed, true) ? $category : 'others',
            'is_private' => $this->param('is_private') ? 1 : 0,
        ]);

        if ($email !== '') {
            \Queue::email(
                strtolower($email),
                'We are praying with you',
                '<p>Dear ' . \Helpers::escape($name) . ',</p>'
                . '<p>Your prayer request &ldquo;' . \Helpers::escape($subject) . '&rdquo; has been received. '
                . 'Our prayer team will stand with you in agreement.</p>'
                . '<p>In His love,<br>' . \Helpers::escape(\Settings::get('site_name', SITE_NAME)) . '</p>'
            );
        }

        $this->ok([], 'Your prayer request has been received. Our prayer team will stand with you.');
    }

    /** POST /api/prayer/praying — increment the "I'm praying" counter. */
    public function prayerPraying(): void {
        $this->requirePost();
        $this->guardCsrf();
        $id = (int) $this->param('id', 0);
        $row = \Database::fetchOne('SELECT id FROM prayer_requests WHERE id = ? AND is_private = 0', [$id]);
        if (!$row) {
            $this->fail('Prayer request not found.', [], 404);
        }
        \Database::execute('UPDATE prayer_requests SET prayer_count = prayer_count + 1 WHERE id = ?', [$id]);
        $count = (int) \Database::fetchColumn('SELECT prayer_count FROM prayer_requests WHERE id = ?', [$id]);
        $this->ok(['prayer_count' => $count], 'Thank you for praying.');
    }

    /* ------------------------------------------------------------------ */
    /* Newsletter & Contact                                                */
    /* ------------------------------------------------------------------ */

    /** POST /api/newsletter/subscribe. */
    public function newsletterSubscribe(): void {
        $this->requirePost();
        $this->guardCsrf();
        $email = strtolower((string) $this->param('email', ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->fail('Enter a valid email address.', ['email' => 'Invalid email.'], 422);
        }
        $existing = \Database::fetchOne('SELECT id, is_active FROM newsletter_subscribers WHERE email = ?', [$email]);
        if ($existing) {
            if (!$existing['is_active']) {
                \Database::update('newsletter_subscribers', ['is_active' => 1], 'id = :id', [':id' => $existing['id']]);
            }
            $this->ok([], 'You are already subscribed. Thank you!');
        }
        \Database::insert('newsletter_subscribers', [
            'email' => $email,
            'name' => ($this->param('name') ?: null),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
        $this->ok([], 'You have been subscribed to our newsletter.');
    }

    /** POST /api/contact/send — submit the contact form. */
    public function contactSend(): void {
        $this->requirePost();
        $this->guardCsrf();
        if (!\Recaptcha::verify((string) $this->param('recaptcha_token', ''))) {
            $this->fail('Spam check failed. Please try again.', [], 422);
        }

        $name = (string) $this->param('name', '');
        $email = (string) $this->param('email', '');
        $subject = (string) $this->param('subject', '');
        $message = (string) $this->param('message', '');
        $errors = [];
        if ($name === '') { $errors['name'] = 'Your name is required.'; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Enter a valid email address.'; }
        if ($subject === '') { $errors['subject'] = 'A subject is required.'; }
        if (strlen($message) < 10) { $errors['message'] = 'Please enter a longer message.'; }
        if ($errors) {
            $this->fail('Please correct the errors below.', $errors, 422);
        }

        \Database::insert('contacts', [
            'name' => $name,
            'email' => strtolower($email),
            'phone' => ($this->param('phone') ?: null),
            'subject' => $subject,
            'message' => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $siteName = \Settings::get('site_name', SITE_NAME);
        \Queue::email(
            strtolower($email),
            'We received your message',
            '<p>Dear ' . \Helpers::escape($name) . ',</p>'
            . '<p>Thank you for contacting ' . \Helpers::escape($siteName) . '. We have received your message '
            . 'regarding &ldquo;' . \Helpers::escape($subject) . '&rdquo; and will respond shortly.</p>'
            . '<p>Warm regards,<br>' . \Helpers::escape($siteName) . '</p>'
        );

        $this->ok([], 'Thank you for reaching out. We will get back to you shortly.');
    }

    /* ------------------------------------------------------------------ */
    /* Announcements / Livestream / Search                                 */
    /* ------------------------------------------------------------------ */

    /** GET /api/announcements — active, website-visible announcements. */
    public function announcements(): void {
        $today = date('Y-m-d');
        $rows = \Database::fetchAll(
            "SELECT id, title, body, type, start_date, end_date
             FROM announcements
             WHERE is_active = 1 AND show_on_website = 1
               AND start_date <= ?
               AND (end_date IS NULL OR end_date >= ?)
             ORDER BY FIELD(type, 'urgent', 'pastoral', 'event', 'general'), start_date DESC
             LIMIT 20",
            [$today, $today]
        );
        $this->ok(['announcements' => $rows]);
    }

    /** GET /api/livestream/status — is a stream live right now? */
    public function livestreamStatus(): void {
        $isLive = (string) \Settings::get('livestream_is_live', '0') === '1';
        $this->ok([
            'is_live' => $isLive,
            'url' => \Settings::get('livestream_url', ''),
            'channel_id' => \Settings::get('livestream_channel_id', ''),
            'offline_message' => \Settings::get('livestream_offline_message', 'We are currently offline. Join us at our next service.'),
        ]);
    }

    /** GET /api/search?q= — global grouped search for the nav dropdown. */
    public function search(): void {
        $q = (string) $this->param('q', '');
        $results = ['sermons' => [], 'events' => [], 'ministries' => [], 'blog' => []];
        if (strlen($q) >= 2) {
            $term = '%' . $q . '%';
            $results['sermons'] = \Database::fetchAll('SELECT title, slug, preacher FROM sermons WHERE is_published = 1 AND (title LIKE ? OR preacher LIKE ? OR tags LIKE ?) LIMIT 5', [$term, $term, $term]);
            $results['events'] = \Database::fetchAll('SELECT title, slug, event_date FROM events WHERE is_published = 1 AND title LIKE ? LIMIT 5', [$term]);
            $results['ministries'] = \Database::fetchAll('SELECT name AS title, slug FROM ministries WHERE is_active = 1 AND name LIKE ? LIMIT 5', [$term]);
            $results['blog'] = \Database::fetchAll('SELECT title, slug FROM blog_posts WHERE is_published = 1 AND (title LIKE ? OR excerpt LIKE ? OR tags LIKE ?) LIMIT 5', [$term, $term, $term]);
        }
        $this->ok(['query' => $q, 'results' => $results]);
    }
}
