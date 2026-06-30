<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="eyebrow text-rccg-gold">Events</p>
            <h1 class="mt-4 max-w-4xl font-display text-4xl font-extrabold leading-tight md:text-6xl">Gather, grow, and serve together</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100">Explore upcoming parish services, programs, outreach, and community gatherings.</p>
        </div>
    </section>

    <?php if (!empty($events)): ?>
    <section class="public-band pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card"><div id="events-calendar"></div></div>
        </div>
    </section>
    <?php endif; ?>

    <section class="public-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="eyebrow">Calendar</p>
                <h2 class="section-title">Upcoming gatherings</h2>
            </div>
            <div class="grid gap-6 md:grid-cols-2">
                <?php if (empty($events)): ?>
                    <div class="empty-state md:col-span-2">
                        <span class="empty-state-icon"><i class="fas fa-calendar-days"></i></span>
                        <h2 class="text-2xl font-extrabold text-rccg-navy">No events published yet</h2>
                        <p class="mt-2 text-gray-600">Upcoming events will appear here after they are added from the admin area. Join us at our regular services this week.</p>
                        <a href="<?php echo BASE_URL; ?>/contact" class="btn-primary mt-6 px-5 py-3">View Service Times</a>
                    </div>
                <?php endif; ?>

                <?php foreach ($events as $event): ?>
                    <article class="card">
                        <p class="meta-pill"><i class="fas fa-calendar"></i><?php echo Helpers::escape(Helpers::formatDate($event['event_date'], 'M d, Y')); ?></p>
                        <h2 class="mt-4 text-2xl font-extrabold text-rccg-navy"><?php echo Helpers::escape($event['title']); ?></h2>
                        <p class="mt-3 text-gray-600"><?php echo Helpers::escape($event['short_description'] ?: 'More details are available on the event page.'); ?></p>
                        <p class="mt-4 text-sm font-bold text-gray-500"><i class="fas fa-location-dot mr-2 text-rccg-red"></i><?php echo Helpers::escape($event['venue'] ?: 'Venue to be announced'); ?></p>
                        <a href="<?php echo BASE_URL; ?>/events/<?php echo Helpers::escape($event['slug']); ?>" class="mt-5 inline-flex font-extrabold text-rccg-red">View event</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php if (!empty($events)): ?>
<?php
$calendarEvents = array_map(static fn($e) => [
    'title' => $e['title'],
    'start' => $e['event_date'],
    'url' => BASE_URL . '/events/' . $e['slug'],
], $events);
?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('events-calendar');
    if (el && window.FullCalendar) {
        new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            height: 'auto',
            events: <?php echo json_encode($calendarEvents, JSON_UNESCAPED_SLASHES); ?>,
            eventColor: '#d7193f',
            eventClick: function (info) {
                if (info.event.url) { window.location.href = info.event.url; info.jsEvent.preventDefault(); }
            }
        }).render();
    }
});
</script>
<?php endif; ?>
