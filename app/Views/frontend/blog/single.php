<main>
    <section class="page-hero">
        <div class="hero-inner max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="eyebrow text-rccg-gold">Blog</p>
            <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight md:text-6xl"><?php echo Helpers::escape($post['title']); ?></h1>
            <p class="mt-5 text-gray-100"><?php echo Helpers::escape(Helpers::formatDate($post['published_at'] ?: $post['created_at'], 'M d, Y')); ?></p>
        </div>
    </section>

    <section class="public-band py-16">
        <article class="card content-prose max-w-4xl mx-auto px-4 sm:px-8">
            <?php echo Helpers::safeHtml($post['body']); ?>
        </article>
        <div class="max-w-4xl mx-auto px-4 mt-8">
            <a href="<?php echo BASE_URL; ?>/blog" class="font-extrabold text-rccg-red">Back to blog</a>
        </div>
    </section>
</main>
