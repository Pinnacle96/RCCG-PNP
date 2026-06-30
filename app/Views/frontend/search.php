<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="eyebrow text-rccg-gold">Search</p>
            <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight md:text-6xl">Find sermons, events, ministries, and updates</h1>
            <form class="mt-8 grid max-w-3xl gap-3 sm:grid-cols-[1fr_auto]" method="get" action="<?php echo BASE_URL; ?>/search">
                <input class="form-input text-gray-900" name="q" value="<?php echo Helpers::escape($q); ?>" placeholder="Search the site">
                <button class="btn-primary px-6" type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
    </section>

    <section class="public-band py-16">
        <div class="max-w-7xl mx-auto px-4 space-y-10">
            <?php if ($q === ''): ?>
                <div class="empty-state">
                    <span class="empty-state-icon"><i class="fas fa-search"></i></span>
                    <h2 class="text-2xl font-extrabold text-rccg-navy">Start with a keyword</h2>
                    <p class="mt-2 text-gray-600">Search for a sermon title, event, ministry, or parish update.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($results as $type => $items): ?>
                <div>
                    <h2 class="mb-4 text-2xl font-extrabold text-rccg-navy capitalize"><?php echo Helpers::escape($type); ?></h2>
                    <?php if ($q !== '' && empty($items)): ?>
                        <p class="rounded-lg bg-white p-4 text-gray-500 shadow-sm">No results in <?php echo Helpers::escape($type); ?>.</p>
                    <?php endif; ?>
                    <div class="grid gap-4 md:grid-cols-3">
                        <?php foreach ($items as $item): ?>
                            <article class="card">
                                <p class="meta-pill"><i class="fas fa-circle-info"></i><?php echo Helpers::escape($type); ?></p>
                                <h3 class="mt-4 font-extrabold text-rccg-navy"><?php echo Helpers::escape($item['title']); ?></h3>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
