<main class="soft-band py-20">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <span class="empty-state-icon"><i class="fas fa-map-signs"></i></span>
        <p class="eyebrow">404</p>
        <h1 class="mt-3 font-display text-4xl font-extrabold text-rccg-navy md:text-6xl">Page Not Found</h1>
        <p class="mx-auto mt-4 max-w-2xl text-gray-600">The page you requested could not be found. Try search, sermons, events, or return home.</p>
        <form method="get" action="<?php echo BASE_URL; ?>/search" class="mx-auto mt-8 grid max-w-xl gap-3 sm:grid-cols-[1fr_auto]">
            <input class="form-input text-left" name="q" placeholder="Search the site">
            <button class="btn-primary px-5" type="submit">Search</button>
        </form>
        <a href="<?php echo BASE_URL; ?>" class="btn-secondary mt-6 px-6 py-3">Return Home</a>
    </div>
</main>
