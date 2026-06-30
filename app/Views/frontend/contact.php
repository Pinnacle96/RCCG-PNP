<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 lg:grid-cols-[1fr_0.8fr] lg:items-end">
            <div>
                <p class="eyebrow text-rccg-gold">Contact</p>
                <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight md:text-6xl">We would love to hear from you</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100">Send a message, ask a question, request pastoral care, or plan your visit. Our team will respond as soon as possible.</p>
            </div>
            <div class="page-hero-card">
                <p class="text-sm font-extrabold uppercase tracking-wide text-rccg-gold">Service times</p>
                <ul class="mt-4 space-y-3 text-gray-100">
                    <li><?php echo Helpers::escape($settings['service_sunday_first'] ?? SERVICE_SUNDAY_FIRST); ?></li>
                    <li><?php echo Helpers::escape($settings['service_wednesday'] ?? SERVICE_WEDNESDAY); ?></li>
                    <li><?php echo Helpers::escape($settings['service_friday'] ?? SERVICE_FRIDAY); ?></li>
                </ul>
            </div>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 lg:grid-cols-[0.85fr_1.15fr]">
            <div class="space-y-4">
                <article class="card">
                    <span class="feature-icon"><i class="fas fa-location-dot"></i></span>
                    <h2 class="mt-4 text-xl font-extrabold text-rccg-navy">Address</h2>
                    <p class="mt-2 text-gray-600"><?php echo Helpers::escape($settings['church_address'] ?? CHURCH_ADDRESS); ?></p>
                </article>
                <article class="card">
                    <span class="feature-icon"><i class="fas fa-phone"></i></span>
                    <h2 class="mt-4 text-xl font-extrabold text-rccg-navy">Phone</h2>
                    <p class="mt-2 text-gray-600"><?php echo Helpers::escape($settings['church_phone'] ?? CHURCH_PHONE); ?></p>
                </article>
                <article class="card">
                    <span class="feature-icon"><i class="fas fa-envelope"></i></span>
                    <h2 class="mt-4 text-xl font-extrabold text-rccg-navy">Email</h2>
                    <p class="mt-2 text-gray-600"><?php echo Helpers::escape($settings['church_email'] ?? CHURCH_EMAIL); ?></p>
                </article>
            </div>

            <form method="post" action="<?php echo BASE_URL; ?>/contact" data-validate class="form-panel space-y-5">
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                <div>
                    <p class="eyebrow">Send a message</p>
                    <h2 class="mt-2 text-2xl font-extrabold text-rccg-navy">How can we help?</h2>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div><label class="form-label" for="name">Name</label><input class="form-input" id="name" name="name" required></div>
                    <div><label class="form-label" for="email">Email</label><input class="form-input" id="email" type="email" name="email" required></div>
                    <div><label class="form-label" for="phone">Phone</label><input class="form-input" id="phone" name="phone"></div>
                    <div><label class="form-label" for="subject">Subject</label><input class="form-input" id="subject" name="subject" required></div>
                </div>
                <div>
                    <label class="form-label" for="message">Message</label>
                    <textarea class="form-input" id="message" name="message" rows="6" required></textarea>
                </div>
                <button class="btn-primary px-6 py-3" type="submit"><i class="fas fa-paper-plane"></i> Send Message</button>
            </form>
        </div>
    </section>
</main>
