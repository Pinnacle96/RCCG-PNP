<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="eyebrow text-rccg-gold">Gallery</p>
            <h1 class="mt-4 max-w-4xl font-display text-4xl font-extrabold leading-tight md:text-6xl">Parish moments and memories</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100">See worship, fellowship, outreach, and family life from our church community.</p>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-6 md:grid-cols-3">
            <?php if (empty($albums)): ?>
                <div class="empty-state md:col-span-3">
                    <span class="empty-state-icon"><i class="fas fa-images"></i></span>
                    <h2 class="text-2xl font-extrabold text-rccg-navy">No albums yet</h2>
                    <p class="mt-2 text-gray-600">Photo albums will appear here once published from the admin area.</p>
                    <a href="<?php echo BASE_URL; ?>/events" class="btn-primary mt-6 px-5 py-3">Explore Events</a>
                </div>
            <?php endif; ?>
            <?php foreach ($albums as $album): ?>
                <article class="card">
                    <span class="feature-icon"><i class="fas fa-camera"></i></span>
                    <h2 class="mt-5 text-xl font-extrabold text-rccg-navy"><?php echo Helpers::escape($album['title']); ?></h2>
                    <p class="mt-3 text-gray-600"><?php echo Helpers::escape($album['description'] ?: 'View photos from this album.'); ?></p>
                    <a href="<?php echo BASE_URL; ?>/gallery/<?php echo Helpers::escape($album['slug']); ?>" class="mt-5 inline-flex font-extrabold text-rccg-red">Open album</a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
