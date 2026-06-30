<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 lg:grid-cols-[1fr_0.75fr] lg:items-end">
            <div>
                <p class="eyebrow text-rccg-gold">Sermons</p>
                <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight md:text-6xl">Messages for faith and life</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100">Listen to recent teachings, worship messages, and sermon series from our parish.</p>
            </div>
            <div class="page-hero-card">
                <p class="text-sm font-extrabold uppercase tracking-wide text-rccg-gold">Grow deeper</p>
                <p class="mt-3 text-gray-100">Make space for scripture, prayer, and practical teaching throughout your week.</p>
            </div>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (!empty($seriesList)): ?>
                <div class="mb-10">
                    <p class="eyebrow">Series</p>
                    <h2 class="section-title">Explore sermon series</h2>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <?php foreach ($seriesList as $series): ?>
                            <a class="meta-pill hover:border-rccg-red hover:text-rccg-red" href="<?php echo BASE_URL; ?>/sermons/series/<?php echo Helpers::escape($series['slug']); ?>">
                                <i class="fas fa-layer-group"></i><?php echo Helpers::escape($series['title']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="grid gap-6 md:grid-cols-3">
                <?php if (empty($sermons)): ?>
                    <div class="empty-state md:col-span-3">
                        <span class="empty-state-icon"><i class="fas fa-microphone-lines"></i></span>
                        <h2 class="text-2xl font-extrabold text-rccg-navy">No sermons published yet</h2>
                        <p class="mt-2 text-gray-600">Sermons will appear here after they are added from the admin area. You can still connect with us through prayer, events, and service times.</p>
                        <a href="<?php echo BASE_URL; ?>/contact" class="btn-primary mt-6 px-5 py-3">Plan a Visit</a>
                    </div>
                <?php endif; ?>

                <?php foreach ($sermons as $sermon): ?>
                    <article class="card">
                        <p class="meta-pill"><i class="fas fa-calendar"></i><?php echo Helpers::escape(Helpers::formatDate($sermon['sermon_date'], 'M d, Y')); ?></p>
                        <h2 class="mt-4 text-xl font-extrabold text-rccg-navy"><?php echo Helpers::escape($sermon['title']); ?></h2>
                        <p class="mt-2 font-semibold text-gray-600"><?php echo Helpers::escape($sermon['preacher']); ?></p>
                        <?php if (!empty($sermon['scripture_ref'])): ?>
                            <p class="mt-3 text-sm font-extrabold text-rccg-red"><?php echo Helpers::escape($sermon['scripture_ref']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($sermon['series_title'])): ?>
                            <a href="<?php echo BASE_URL; ?>/sermons/series/<?php echo Helpers::escape($sermon['series_slug']); ?>" class="mt-4 inline-flex text-sm font-extrabold text-rccg-navy">
                                <?php echo Helpers::escape($sermon['series_title']); ?>
                            </a>
                        <?php endif; ?>
                        <div><a href="<?php echo BASE_URL; ?>/sermons/<?php echo Helpers::escape($sermon['slug']); ?>" class="mt-5 inline-flex font-extrabold text-rccg-red">Open message</a></div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="mt-8 flex items-center justify-between">
                    <p class="text-sm font-bold text-gray-600">Page <?php echo (int) $page; ?> of <?php echo (int) $totalPages; ?></p>
                    <div class="flex gap-2">
                        <?php if ($page > 1): ?><a class="btn-secondary px-4 py-2" href="<?php echo BASE_URL; ?>/sermons?page=<?php echo $page - 1; ?>">Previous</a><?php endif; ?>
                        <?php if ($page < $totalPages): ?><a class="btn-primary px-4 py-2" href="<?php echo BASE_URL; ?>/sermons?page=<?php echo $page + 1; ?>">Next</a><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
