<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="eyebrow text-rccg-gold">Gallery</p>
            <h1 class="mt-4 max-w-4xl font-display text-4xl font-extrabold leading-tight md:text-6xl"><?php echo Helpers::escape($album['title']); ?></h1>
            <?php if (!empty($album['description'])): ?><p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100"><?php echo Helpers::escape($album['description']); ?></p><?php endif; ?>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-6 md:grid-cols-3">
            <?php if (empty($photos)): ?>
                <div class="empty-state md:col-span-3">
                    <span class="empty-state-icon"><i class="fas fa-camera"></i></span>
                    <h2 class="text-2xl font-extrabold text-rccg-navy">Photos coming soon</h2>
                    <p class="mt-2 text-gray-600">Photos will appear here once uploaded from the admin area.</p>
                </div>
            <?php endif; ?>
            <?php foreach ($photos as $photo): ?>
                <figure class="card media-card">
                    <?php if (!empty($photo['file_path'])): ?>
                        <img src="<?php echo Helpers::escape(UPLOAD_URL . 'gallery/' . $photo['file_path']); ?>" alt="<?php echo Helpers::escape($photo['title'] ?: 'Gallery item'); ?>">
                    <?php else: ?>
                        <div class="flex aspect-video items-center justify-center bg-gray-100 text-gray-500">Media</div>
                    <?php endif; ?>
                    <figcaption class="media-card-body">
                        <p class="font-extrabold text-rccg-navy"><?php echo Helpers::escape($photo['title'] ?: 'Gallery item'); ?></p>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
        <div class="max-w-7xl mx-auto px-4 mt-8">
            <a href="<?php echo BASE_URL; ?>/gallery" class="font-extrabold text-rccg-red">Back to gallery</a>
        </div>
    </section>
</main>
