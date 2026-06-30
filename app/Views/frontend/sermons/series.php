<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="eyebrow text-rccg-gold">Sermon Series</p>
            <h1 class="mt-4 max-w-4xl font-display text-4xl font-extrabold leading-tight md:text-6xl"><?php echo Helpers::escape($series['title']); ?></h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100"><?php echo Helpers::escape($series['description'] ?: 'Messages gathered around one theme for deeper study and growth.'); ?></p>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-3">
                <?php if (empty($sermons)): ?>
                    <div class="empty-state md:col-span-3">
                        <span class="empty-state-icon"><i class="fas fa-layer-group"></i></span>
                        <h2 class="text-2xl font-extrabold text-rccg-navy">No messages in this series yet</h2>
                        <p class="mt-2 text-gray-600">Published sermons assigned to this series will appear here.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($sermons as $sermon): ?>
                    <article class="card">
                        <p class="meta-pill"><i class="fas fa-calendar"></i><?php echo Helpers::escape(Helpers::formatDate($sermon['sermon_date'], 'M d, Y')); ?></p>
                        <h2 class="mt-4 text-xl font-extrabold text-rccg-navy"><?php echo Helpers::escape($sermon['title']); ?></h2>
                        <p class="mt-2 text-gray-600"><?php echo Helpers::escape($sermon['preacher']); ?></p>
                        <a href="<?php echo BASE_URL; ?>/sermons/<?php echo Helpers::escape($sermon['slug']); ?>" class="mt-5 inline-flex font-extrabold text-rccg-red">Open message</a>
                    </article>
                <?php endforeach; ?>
            </div>

            <a href="<?php echo BASE_URL; ?>/sermons" class="mt-8 inline-flex font-extrabold text-rccg-red">Back to sermons</a>
        </div>
    </section>
</main>
