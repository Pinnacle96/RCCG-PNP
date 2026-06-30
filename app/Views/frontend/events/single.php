<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="eyebrow text-rccg-gold"><?php echo Helpers::escape(Helpers::formatDate($event['event_date'])); ?></p>
            <h1 class="mt-4 max-w-4xl font-display text-4xl font-extrabold leading-tight md:text-6xl"><?php echo Helpers::escape($event['title']); ?></h1>
            <p class="mt-4 text-xl font-semibold text-gray-100"><?php echo Helpers::escape($event['venue'] ?: 'Venue to be announced'); ?></p>
        </div>
    </section>

    <section class="public-band py-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="card">
                    <p class="text-sm font-extrabold uppercase tracking-wide text-gray-500">Date</p>
                    <p class="mt-2 font-extrabold text-rccg-navy"><?php echo Helpers::escape(Helpers::formatDate($event['event_date'])); ?></p>
                </div>
                <div class="card">
                    <p class="text-sm font-extrabold uppercase tracking-wide text-gray-500">Time</p>
                    <p class="mt-2 font-extrabold text-rccg-navy"><?php echo Helpers::escape($event['start_time'] ? date('g:i A', strtotime($event['start_time'])) : 'To be announced'); ?></p>
                </div>
                <div class="card">
                    <p class="text-sm font-extrabold uppercase tracking-wide text-gray-500">Venue</p>
                    <p class="mt-2 font-extrabold text-rccg-navy"><?php echo Helpers::escape($event['venue'] ?: 'To be announced'); ?></p>
                </div>
            </div>

            <article class="card content-prose">
                <?php echo nl2br(Helpers::escape((string) ($event['description'] ?: $event['short_description'] ?: 'Event details will be available soon.'))); ?>
            </article>

            <?php if (!empty($event['requires_registration'])): ?>
                <form method="post" action="<?php echo BASE_URL; ?>/events/<?php echo Helpers::escape($event['slug']); ?>/register" class="form-panel space-y-4">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                    <div>
                        <p class="eyebrow">Registration</p>
                        <h2 class="mt-2 text-2xl font-extrabold text-rccg-navy">Register for this event</h2>
                        <p class="mt-2 text-gray-600">
                            <?php echo (int) $registrationCount; ?> registration<?php echo (int) $registrationCount === 1 ? '' : 's'; ?> received.
                            <?php if (!empty($event['registration_limit'])): ?> Limit: <?php echo (int) $event['registration_limit']; ?>.<?php endif; ?>
                        </p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div><label class="form-label" for="name">Name</label><input class="form-input" id="name" name="name" required></div>
                        <div><label class="form-label" for="email">Email</label><input class="form-input" id="email" type="email" name="email"></div>
                        <div><label class="form-label" for="phone">Phone</label><input class="form-input" id="phone" name="phone"></div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="form-label" for="adults">Adults</label><input class="form-input" id="adults" type="number" min="1" name="adults" value="1"></div>
                            <div><label class="form-label" for="children">Children</label><input class="form-input" id="children" type="number" min="0" name="children" value="0"></div>
                        </div>
                    </div>
                    <button class="btn-primary px-6 py-3" type="submit"><i class="fas fa-check"></i> Submit Registration</button>
                </form>
            <?php endif; ?>

            <a href="<?php echo BASE_URL; ?>/events" class="font-extrabold text-rccg-red">Back to events</a>
        </div>
    </section>
</main>
