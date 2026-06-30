<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="eyebrow text-rccg-gold">Blog</p>
            <h1 class="mt-4 max-w-4xl font-display text-4xl font-extrabold leading-tight md:text-6xl">News, reflections, and parish updates</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100">Read stories, announcements, and encouragement from the life of the church.</p>
        </div>
    </section>

    <section class="public-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-3">
                <?php if (empty($posts)): ?>
                    <div class="empty-state md:col-span-3">
                        <span class="empty-state-icon"><i class="fas fa-newspaper"></i></span>
                        <h2 class="text-2xl font-extrabold text-rccg-navy">No posts yet</h2>
                        <p class="mt-2 text-gray-600">Blog posts will appear here once published. Check back soon for parish news and reflections.</p>
                        <a href="<?php echo BASE_URL; ?>/events" class="btn-primary mt-6 px-5 py-3">See Events</a>
                    </div>
                <?php endif; ?>

                <?php foreach ($posts as $post): ?>
                    <article class="card">
                        <p class="meta-pill"><i class="fas fa-calendar"></i><?php echo Helpers::escape(Helpers::formatDate($post['published_at'] ?: $post['created_at'], 'M d, Y')); ?></p>
                        <h2 class="mt-4 text-xl font-extrabold text-rccg-navy"><?php echo Helpers::escape($post['title']); ?></h2>
                        <p class="mt-3 text-gray-600"><?php echo Helpers::escape($post['excerpt'] ?: 'Read this update from the parish.'); ?></p>
                        <a href="<?php echo BASE_URL; ?>/blog/<?php echo Helpers::escape($post['slug']); ?>" class="mt-5 inline-flex font-extrabold text-rccg-red">Read more</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
