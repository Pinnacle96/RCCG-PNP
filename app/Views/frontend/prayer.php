<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="eyebrow text-rccg-gold">Prayer</p>
                <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight md:text-6xl">Let us pray with you</h1>
                <p class="mt-6 text-lg leading-8 text-gray-100">You do not have to carry burdens alone. Share your request with the prayer team, privately or publicly.</p>
            </div>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 lg:grid-cols-[0.95fr_1.05fr]">
            <form method="post" action="<?php echo BASE_URL; ?>/prayer" class="form-panel space-y-4">
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                <div>
                    <p class="eyebrow">Submit request</p>
                    <h2 class="mt-2 text-2xl font-extrabold text-rccg-navy">How can we pray?</h2>
                </div>
                <div><label class="form-label">Your name</label><input class="form-input" name="requester_name" required></div>
                <div><label class="form-label">Email</label><input class="form-input" type="email" name="email"></div>
                <div><label class="form-label">Subject</label><input class="form-input" name="subject" required></div>
                <div><label class="form-label">Prayer request</label><textarea class="form-input" name="request_text" rows="6" required></textarea></div>
                <label class="flex items-center gap-3 text-sm font-semibold text-gray-600"><input class="form-checkbox" type="checkbox" name="is_private" value="1"> Keep this request private</label>
                <button class="btn-primary px-6 py-3"><i class="fas fa-hands-praying"></i> Submit Request</button>
            </form>

            <div>
                <p class="eyebrow">Prayer wall</p>
                <h2 class="section-title">Standing together in faith</h2>
                <div class="mt-6 space-y-4">
                    <?php if (empty($requests)): ?>
                        <div class="empty-state text-left">
                            <span class="empty-state-icon"><i class="fas fa-hands-praying"></i></span>
                            <h3 class="text-xl font-extrabold text-rccg-navy">No public requests yet</h3>
                            <p class="mt-2 text-gray-600">Public prayer requests will appear here. Private requests are sent only to the prayer team.</p>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($requests as $request): ?>
                        <article class="card">
                            <p class="meta-pill"><i class="fas fa-heart"></i> Prayer request</p>
                            <h3 class="mt-4 text-xl font-extrabold text-rccg-navy"><?php echo Helpers::escape($request['subject']); ?></h3>
                            <p class="mt-2 text-gray-600"><?php echo Helpers::escape($request['request_text']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>
