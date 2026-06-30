<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="eyebrow text-rccg-gold">Ministries</p>
            <h1 class="mt-4 max-w-4xl font-display text-4xl font-extrabold leading-tight md:text-6xl">Find a place to serve and grow</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100">Use your gifts, build friendships, and strengthen the church family through ministry.</p>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="eyebrow">Serve</p>
                <h2 class="section-title">Ministry opportunities</h2>
            </div>
            <div class="grid gap-6 md:grid-cols-3">
                <?php if (empty($ministries)): ?>
                    <div class="empty-state md:col-span-3">
                        <span class="empty-state-icon"><i class="fas fa-handshake-angle"></i></span>
                        <h2 class="text-2xl font-extrabold text-rccg-navy">No ministries published yet</h2>
                        <p class="mt-2 text-gray-600">Ministries will appear here after they are added from the admin area. You can still contact us to ask where help is needed.</p>
                        <a href="<?php echo BASE_URL; ?>/contact" class="btn-primary mt-6 px-5 py-3">Ask About Serving</a>
                    </div>
                <?php endif; ?>

                <?php foreach ($ministries as $ministry): ?>
                    <article class="card">
                        <span class="feature-icon"><i class="fas fa-people-group"></i></span>
                        <h2 class="mt-5 text-xl font-extrabold text-rccg-navy"><?php echo Helpers::escape($ministry['name']); ?></h2>
                        <p class="mt-3 text-gray-600"><?php echo Helpers::escape($ministry['short_desc'] ?: 'Serve and grow with this ministry.'); ?></p>
                        <a href="<?php echo BASE_URL; ?>/ministries/<?php echo Helpers::escape($ministry['slug']); ?>" class="mt-5 inline-flex font-extrabold text-rccg-red">Learn more</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cta-band py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="eyebrow text-rccg-gold">Next step</p>
                <h2 class="mt-2 font-display text-3xl font-bold">Not sure where to serve?</h2>
                <p class="mt-2 text-gray-200">Start with membership or speak with the church office and we will help you find a fit.</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/join" class="btn-primary px-5 py-3">Become a Member</a>
        </div>
    </section>
</main>
