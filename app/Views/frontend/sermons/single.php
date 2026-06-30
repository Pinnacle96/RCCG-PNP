<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="eyebrow text-rccg-gold"><?php echo Helpers::escape(Helpers::formatDate($sermon['sermon_date'])); ?></p>
            <h1 class="mt-4 max-w-4xl font-display text-4xl font-extrabold leading-tight md:text-6xl"><?php echo Helpers::escape($sermon['title']); ?></h1>
            <p class="mt-4 text-xl font-semibold text-gray-100"><?php echo Helpers::escape($sermon['preacher']); ?></p>
        </div>
    </section>

    <section class="public-band py-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="card">
                    <p class="text-sm font-extrabold uppercase tracking-wide text-gray-500">Date</p>
                    <p class="mt-2 font-extrabold text-rccg-navy"><?php echo Helpers::escape(Helpers::formatDate($sermon['sermon_date'])); ?></p>
                </div>
                <div class="card">
                    <p class="text-sm font-extrabold uppercase tracking-wide text-gray-500">Preacher</p>
                    <p class="mt-2 font-extrabold text-rccg-navy"><?php echo Helpers::escape($sermon['preacher']); ?></p>
                </div>
                <div class="card">
                    <p class="text-sm font-extrabold uppercase tracking-wide text-gray-500">Scripture</p>
                    <p class="mt-2 font-extrabold text-rccg-navy"><?php echo Helpers::escape($sermon['scripture_ref'] ?: 'To be added'); ?></p>
                </div>
            </div>

            <?php if (!empty($sermon['audio_file'])): ?>
                <div class="card">
                    <h2 class="mb-4 text-xl font-extrabold text-rccg-navy">Listen to message</h2>
                    <audio id="sermon-player" class="w-full" controls>
                        <source src="<?php echo UPLOAD_URL; ?>sermons/<?php echo Helpers::escape($sermon['audio_file']); ?>" type="audio/mpeg">
                    </audio>
                </div>
            <?php endif; ?>

            <article class="card content-prose">
                <?php echo nl2br(Helpers::escape((string) ($sermon['description'] ?: 'Sermon notes will be available soon.'))); ?>
                <?php if (!empty($sermon['video_url'])): ?>
                    <p><a class="btn-primary px-6 py-3" href="<?php echo Helpers::escape($sermon['video_url']); ?>" target="_blank" rel="noopener"><i class="fas fa-play"></i> Watch Video</a></p>
                <?php endif; ?>
            </article>

            <a href="<?php echo BASE_URL; ?>/sermons" class="font-extrabold text-rccg-red">Back to sermons</a>
        </div>
    </section>
</main>

<?php if (!empty($sermon['audio_file'])): ?>
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css">
<script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.Plyr) { new Plyr('#sermon-player'); }
});
</script>
<?php endif; ?>
