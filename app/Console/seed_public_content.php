<?php

require_once dirname(__DIR__) . '/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    exit('This command must be run from the command line.');
}

$series = new \App\Models\SermonSeriesModel();
$seriesRow = $series->findBy('slug', 'faith-for-the-journey-series');
if (!$seriesRow) {
    $seriesId = $series->create([
        'title' => 'Faith for the Journey Series',
        'slug' => 'faith-for-the-journey-series',
        'description' => 'Messages for growing steady faith through ordinary and difficult seasons.',
        'start_date' => date('Y-m-d'),
        'is_active' => 1,
    ]);
    $seriesRow = $series->find((int) $seriesId);
    fwrite(STDOUT, "Seeded sermon series\n");
}

$sermon = new \App\Models\SermonModel();
if (!$sermon->findBy('slug', 'faith-for-the-journey')) {
    $sermon->create([
        'title' => 'Faith for the Journey',
        'slug' => 'faith-for-the-journey',
        'preacher' => 'Pastor',
        'scripture_ref' => 'Hebrews 11:1',
        'series_id' => $seriesRow['id'] ?? null,
        'description' => 'A message about trusting God through every season.',
        'sermon_date' => date('Y-m-d'),
        'sermon_type' => 'sunday',
        'is_published' => 1,
        'is_featured' => 1,
    ]);
    fwrite(STDOUT, "Seeded sermon\n");
} else {
    \Database::update('sermons', ['series_id' => $seriesRow['id'] ?? null], 'slug = :slug AND series_id IS NULL', [':slug' => 'faith-for-the-journey']);
}

$event = new \App\Models\EventModel();
if (!$event->findBy('slug', 'sunday-worship-service')) {
    $event->create([
        'title' => 'Sunday Worship Service',
        'slug' => 'sunday-worship-service',
        'description' => 'Join us for worship, prayer, teaching, and fellowship.',
        'short_description' => 'Weekly worship service for the whole family.',
        'event_date' => date('Y-m-d', strtotime('+7 days')),
        'start_time' => '10:00:00',
        'venue' => 'Church Auditorium',
        'requires_registration' => 1,
        'registration_limit' => 200,
        'is_published' => 1,
        'is_featured' => 1,
    ]);
    fwrite(STDOUT, "Seeded event\n");
} else {
    \Database::update('events', ['requires_registration' => 1, 'registration_limit' => 200], 'slug = :slug', [':slug' => 'sunday-worship-service']);
}

$ministry = new \App\Models\MinistryModel();
if (!$ministry->findBy('slug', 'choir-ministry')) {
    $ministry->create([
        'name' => 'Choir Ministry',
        'slug' => 'choir-ministry',
        'description' => 'The choir leads the church in worship and supports services with music.',
        'short_desc' => 'Leading worship with excellence and devotion.',
        'leader_name' => 'Ministry Leader',
        'meeting_schedule' => 'Saturday rehearsals',
        'meeting_venue' => 'Church Auditorium',
        'is_active' => 1,
        'display_order' => 1,
    ]);
    fwrite(STDOUT, "Seeded ministry\n");
}

$blog = new \App\Models\BlogModel();
if (!$blog->findBy('slug', 'welcome-to-our-parish-family')) {
    $blog->create([
        'title' => 'Welcome to Our Parish Family',
        'slug' => 'welcome-to-our-parish-family',
        'excerpt' => 'A short welcome note for visitors and new members.',
        'body' => 'We are glad to welcome you to RCCG Prince and Princess Parish. Our prayer is that you find faith, family, and purpose here.',
        'is_published' => 1,
        'is_featured' => 1,
        'published_at' => date('Y-m-d H:i:s'),
    ]);
    fwrite(STDOUT, "Seeded blog post\n");
}

$gallery = new \App\Models\GalleryModel();
if (!$gallery->findBy('slug', 'parish-life')) {
    $albumId = $gallery->create([
        'title' => 'Parish Life',
        'slug' => 'parish-life',
        'description' => 'Moments from worship, fellowship, and service.',
        'event_date' => date('Y-m-d'),
        'is_published' => 1,
    ]);
    \Database::insert('gallery', [
        'album_id' => $albumId,
        'title' => 'Worship moment',
        'file_path' => 'defaults/gallery-placeholder.jpg',
        'file_type' => 'image',
        'is_featured' => 1,
        'sort_order' => 1,
    ]);
    fwrite(STDOUT, "Seeded gallery album\n");
}

fwrite(STDOUT, "Public content seed complete.\n");
