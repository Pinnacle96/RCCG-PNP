<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="eyebrow text-rccg-gold">Ministry</p>
            <h1 class="mt-4 max-w-4xl font-display text-4xl font-extrabold leading-tight md:text-6xl"><?php echo Helpers::escape($ministry['name']); ?></h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100"><?php echo Helpers::escape($ministry['short_desc'] ?: 'Serve and grow with this ministry.'); ?></p>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <article class="card content-prose">
                <?php echo nl2br(Helpers::escape((string) ($ministry['description'] ?: 'More details about this ministry will be available soon.'))); ?>
            </article>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="card">
                    <span class="feature-icon"><i class="fas fa-user-tie"></i></span>
                    <p class="mt-4 text-sm font-extrabold uppercase tracking-wide text-gray-500">Leader</p>
                    <p class="mt-2 text-xl font-extrabold text-rccg-navy"><?php echo Helpers::escape($ministry['leader_name'] ?: 'To be announced'); ?></p>
                </div>
                <div class="card">
                    <span class="feature-icon"><i class="fas fa-calendar-check"></i></span>
                    <p class="mt-4 text-sm font-extrabold uppercase tracking-wide text-gray-500">Meeting</p>
                    <p class="mt-2 text-xl font-extrabold text-rccg-navy"><?php echo Helpers::escape($ministry['meeting_schedule'] ?: 'Schedule to be announced'); ?></p>
                </div>
            </div>

            <div class="cta-band rounded-lg p-6 md:p-8">
                <h2 class="font-display text-3xl font-bold">Interested in serving?</h2>
                <p class="mt-2 text-gray-200">Reach out to the church office or submit a membership application so we can help you take the next step.</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="<?php echo BASE_URL; ?>/join" class="btn-primary px-5 py-3">Join the Church</a>
                    <a href="<?php echo BASE_URL; ?>/contact" class="btn-outline px-5 py-3">Contact Us</a>
                </div>
            </div>

            <a href="<?php echo BASE_URL; ?>/ministries" class="font-extrabold text-rccg-red">Back to ministries</a>
        </div>
    </section>
</main>
