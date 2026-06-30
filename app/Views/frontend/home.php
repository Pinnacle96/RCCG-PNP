<?php
/**
 * @var string $siteName
 * @var string $siteTagline
 * @var array $latestSermons
 * @var array $upcomingEvents
 * @var array $ministries
 * @var array $settings
 */
?>
<main>
    <section class="hero-church text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid min-h-[calc(100vh-5rem)] items-center gap-10 py-16 lg:grid-cols-[1.08fr_0.72fr] lg:py-20">
                <div>
                    <p class="mb-5 inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-extrabold uppercase tracking-wide text-rccg-gold">
                        Welcome home
                    </p>
                    <h1 class="font-display max-w-4xl text-4xl font-extrabold leading-tight md:text-6xl lg:text-7xl">
                        <?php echo Helpers::escape($siteName); ?>
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100 md:text-xl">
                        <?php echo Helpers::escape($siteTagline); ?>. A worshipping family growing in faith, serving with love, and building lives for Christ.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="<?php echo BASE_URL; ?>/contact" class="btn-primary px-6 py-3">
                            <i class="fas fa-location-dot"></i>
                            Plan Your Visit
                        </a>
                        <a href="<?php echo BASE_URL; ?>/sermons" class="btn-outline px-6 py-3">
                            <i class="fas fa-play"></i>
                            Watch or Listen
                        </a>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                    <div class="hero-stat">
                        <p class="text-sm font-bold uppercase tracking-wide text-rccg-gold">Sunday Worship</p>
                        <p class="mt-2 text-2xl font-extrabold"><?php echo Helpers::escape($settings['service_sunday_first'] ?? SERVICE_SUNDAY_FIRST); ?></p>
                        <?php if (!empty($settings['service_sunday_second'] ?? SERVICE_SUNDAY_SECOND)): ?>
                            <p class="text-sm text-gray-200"><?php echo Helpers::escape($settings['service_sunday_second'] ?? SERVICE_SUNDAY_SECOND); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="hero-stat">
                        <p class="text-sm font-bold uppercase tracking-wide text-rccg-gold">Midweek & Prayer</p>
                        <p class="mt-2 text-2xl font-extrabold"><?php echo Helpers::escape($settings['service_wednesday'] ?? SERVICE_WEDNESDAY); ?></p>
                        <p class="text-sm text-gray-200"><?php echo Helpers::escape($settings['service_friday'] ?? SERVICE_FRIDAY); ?></p>
                    </div>
                    <div class="hero-stat sm:col-span-2 lg:col-span-1">
                        <p class="text-sm font-bold uppercase tracking-wide text-rccg-gold">Location</p>
                        <p class="mt-2 text-lg font-bold leading-7"><?php echo Helpers::escape($settings['church_address'] ?? CHURCH_ADDRESS); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
            <div>
                <p class="eyebrow">Welcome</p>
                <h2 class="section-title">A place to worship, grow, belong, and serve</h2>
                <p class="content-prose">
                    We are a family of believers passionate about Jesus, prayer, discipleship, and community. Whether you are visiting for the first time or searching for a church home, there is room for you here.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="<?php echo BASE_URL; ?>/about" class="btn-primary px-5 py-3">Who We Are</a>
                    <a href="<?php echo BASE_URL; ?>/join" class="btn-secondary px-5 py-3">Become a Member</a>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <article class="card">
                    <span class="feature-icon"><i class="fas fa-cross"></i></span>
                    <h3 class="mt-4 text-xl font-extrabold text-rccg-navy">Christ Centered</h3>
                    <p class="mt-2 text-gray-600">Every gathering points people toward Jesus and the life of the Spirit.</p>
                </article>
                <article class="card">
                    <span class="feature-icon"><i class="fas fa-book-bible"></i></span>
                    <h3 class="mt-4 text-xl font-extrabold text-rccg-navy">Word & Prayer</h3>
                    <p class="mt-2 text-gray-600">We grow through scripture, prayer, worship, and practical discipleship.</p>
                </article>
                <article class="card">
                    <span class="feature-icon"><i class="fas fa-people-group"></i></span>
                    <h3 class="mt-4 text-xl font-extrabold text-rccg-navy">Family Community</h3>
                    <p class="mt-2 text-gray-600">Children, youth, adults, and families find connection and care.</p>
                </article>
                <article class="card">
                    <span class="feature-icon"><i class="fas fa-hands-helping"></i></span>
                    <h3 class="mt-4 text-xl font-extrabold text-rccg-navy">Purposeful Service</h3>
                    <p class="mt-2 text-gray-600">Everyone has gifts that can strengthen the church and bless others.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[0.82fr_1.18fr] lg:items-center">
                <div class="card media-card">
                    <?php
                    $pastorImage = UPLOAD_PATH . 'pastor/pastor-oluwaseyi-ojo.jpg';
                    $pastorImageUrl = UPLOAD_URL . 'pastor/pastor-oluwaseyi-ojo.jpg';
                    ?>
                    <?php if (is_file($pastorImage)): ?>
                        <img src="<?php echo Helpers::escape($pastorImageUrl); ?>" alt="Pastor Oluwaseyi Ojo" style="height: auto; object-fit: contain;" class="w-full">
                    <?php else: ?>
                        <div class="flex h-[28rem] w-full items-center justify-center bg-[linear-gradient(135deg,#071427,#981331)] text-white">
                            <div class="text-center">
                                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-white/10 text-4xl font-extrabold text-rccg-gold">OO</div>
                                <p class="mt-5 text-sm font-extrabold uppercase tracking-wide text-rccg-gold">Pastor-in-Charge</p>
                                <h3 class="mt-2 font-display text-3xl font-bold">Pastor Oluwaseyi Ojo</h3>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="media-card-body">
                        <p class="text-sm font-extrabold uppercase tracking-wide text-rccg-red">Pastor-in-Charge</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-rccg-navy">Pastor Oluwaseyi Ojo</h3>
                    </div>
                </div>

                <div>
                    <p class="eyebrow">Pastor's welcome</p>
                    <h2 class="section-title">A welcome from our Pastor-in-Charge</h2>
                    <div class="content-prose">
                        <p>Welcome to RCCG Prince and Princess Parish.</p>
                        <p>I am delighted to welcome you to our church family. We are a people called to grow in faith, walk in love, and serve God with sincere hearts. Whether you are worshipping with us for the first time, returning after some time away, or searching for a place to belong, we believe God has a purpose for bringing you here.</p>
                        <p>At RCCG Prince and Princess Parish, our desire is to help every person encounter Christ, grow through the Word and prayer, and become a blessing to their family, community, and generation.</p>
                        <p>We look forward to worshipping with you and walking with you as you take your next step with God.</p>
                    </div>
                    <div class="mt-6 border-l-4 border-rccg-red pl-5">
                        <p class="font-display text-2xl font-bold text-rccg-navy">Pastor Oluwaseyi Ojo</p>
                        <p class="mt-1 text-sm font-extrabold uppercase tracking-wide text-gray-500">Pastor-in-Charge</p>
                    </div>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="<?php echo BASE_URL; ?>/contact" class="btn-primary px-5 py-3">Plan a Visit</a>
                        <a href="<?php echo BASE_URL; ?>/about" class="btn-secondary px-5 py-3">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 text-center">
                <p class="eyebrow">Visit this week</p>
                <h2 class="section-title">Service Times & Gatherings</h2>
                <p class="mx-auto max-w-2xl text-gray-600">Plan your week around worship, teaching, fellowship, and prayer.</p>
            </div>
            <div class="grid gap-5 md:grid-cols-3">
                <article class="card">
                    <p class="meta-pill"><i class="fas fa-sun"></i> Sunday</p>
                    <h3 class="mt-4 text-xl font-extrabold text-rccg-navy">Worship Service</h3>
                    <p class="mt-2 text-gray-600"><?php echo Helpers::escape($settings['service_sunday_first'] ?? SERVICE_SUNDAY_FIRST); ?></p>
                </article>
                <?php if (!empty($settings['service_sunday_second'] ?? SERVICE_SUNDAY_SECOND)): ?>
                <article class="card">
                    <p class="meta-pill"><i class="fas fa-people-roof"></i> Sunday</p>
                    <h3 class="mt-4 text-xl font-extrabold text-rccg-navy">Second Service</h3>
                    <p class="mt-2 text-gray-600"><?php echo Helpers::escape($settings['service_sunday_second'] ?? SERVICE_SUNDAY_SECOND); ?></p>
                </article>
                <?php endif; ?>
                <article class="card">
                    <p class="meta-pill"><i class="fas fa-book-open"></i> Midweek</p>
                    <h3 class="mt-4 text-xl font-extrabold text-rccg-navy">Bible Study</h3>
                    <p class="mt-2 text-gray-600"><?php echo Helpers::escape($settings['service_wednesday'] ?? SERVICE_WEDNESDAY); ?></p>
                </article>
                <article class="card">
                    <p class="meta-pill"><i class="fas fa-hands-praying"></i> Prayer</p>
                    <h3 class="mt-4 text-xl font-extrabold text-rccg-navy">Prayer Night</h3>
                    <p class="mt-2 text-gray-600"><?php echo Helpers::escape($settings['service_friday'] ?? SERVICE_FRIDAY); ?></p>
                </article>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 text-center">
                <p class="eyebrow">Next steps</p>
                <h2 class="section-title">Start your journey here</h2>
            </div>
            <div class="grid gap-5 md:grid-cols-4">
                <article class="card">
                    <span class="feature-icon"><i class="fas fa-location-dot"></i></span>
                    <h3 class="mt-5 text-xl font-extrabold text-rccg-navy">Plan a Visit</h3>
                    <p class="mt-3 text-gray-600">Find service times, contact details, and what to expect when you come.</p>
                    <a href="<?php echo BASE_URL; ?>/contact" class="mt-5 inline-flex font-extrabold text-rccg-red">Visit details</a>
                </article>
                <article class="card">
                    <span class="feature-icon"><i class="fas fa-user-plus"></i></span>
                    <h3 class="mt-5 text-xl font-extrabold text-rccg-navy">Join the Family</h3>
                    <p class="mt-3 text-gray-600">Complete a membership application and let us welcome you properly.</p>
                    <a href="<?php echo BASE_URL; ?>/join" class="mt-5 inline-flex font-extrabold text-rccg-red">Become a member</a>
                </article>
                <article class="card">
                    <span class="feature-icon"><i class="fas fa-hands-praying"></i></span>
                    <h3 class="mt-5 text-xl font-extrabold text-rccg-navy">Request Prayer</h3>
                    <p class="mt-3 text-gray-600">Share your request with the prayer team, privately or publicly.</p>
                    <a href="<?php echo BASE_URL; ?>/prayer" class="mt-5 inline-flex font-extrabold text-rccg-red">Request prayer</a>
                </article>
                <article class="card">
                    <span class="feature-icon"><i class="fas fa-heart"></i></span>
                    <h3 class="mt-5 text-xl font-extrabold text-rccg-navy">Give Online</h3>
                    <p class="mt-3 text-gray-600">Support ministry work securely through tithes and offerings.</p>
                    <a href="<?php echo BASE_URL; ?>/give" class="mt-5 inline-flex font-extrabold text-rccg-red">Give now</a>
                </article>
            </div>
        </div>
    </section>

    <section class="cta-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 lg:grid-cols-[1fr_0.9fr] lg:items-center">
            <div>
                <p class="eyebrow text-rccg-gold">What to expect</p>
                <h2 class="mt-3 font-display text-3xl font-bold md:text-5xl">Warm worship, sound teaching, and a family ready to welcome you</h2>
                <p class="mt-5 max-w-2xl leading-8 text-gray-200">Come ready for heartfelt praise, prayer, biblical teaching, and fellowship. Our team will help you find your way, whether you are coming alone or with family.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="page-hero-card">
                    <p class="text-3xl font-extrabold text-rccg-gold">01</p>
                    <h3 class="mt-2 text-xl font-bold">Arrive</h3>
                    <p class="mt-2 text-sm text-gray-200">Come a little early so we can welcome and guide you.</p>
                </div>
                <div class="page-hero-card">
                    <p class="text-3xl font-extrabold text-rccg-gold">02</p>
                    <h3 class="mt-2 text-xl font-bold">Worship</h3>
                    <p class="mt-2 text-sm text-gray-200">Join in praise, prayer, and the ministry of the Word.</p>
                </div>
                <div class="page-hero-card">
                    <p class="text-3xl font-extrabold text-rccg-gold">03</p>
                    <h3 class="mt-2 text-xl font-bold">Connect</h3>
                    <p class="mt-2 text-sm text-gray-200">Meet someone after service and ask about next steps.</p>
                </div>
                <div class="page-hero-card">
                    <p class="text-3xl font-extrabold text-rccg-gold">04</p>
                    <h3 class="mt-2 text-xl font-bold">Grow</h3>
                    <p class="mt-2 text-sm text-gray-200">Join a group, ministry, or discipleship pathway.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="eyebrow">Messages</p>
                    <h2 class="section-title">Latest Sermons</h2>
                    <p class="max-w-2xl text-gray-600">Catch up on recent teaching and keep growing during the week.</p>
                </div>
                <a href="<?php echo BASE_URL; ?>/sermons" class="font-extrabold text-rccg-red">View all messages</a>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <?php if (empty($latestSermons)): ?>
                    <div class="empty-state md:col-span-3">
                        <span class="empty-state-icon"><i class="fas fa-microphone-lines"></i></span>
                        <h3 class="text-2xl font-extrabold text-rccg-navy">Messages are coming soon</h3>
                        <p class="mt-2 text-gray-600">Published sermons will appear here. Until then, join us in person for worship and teaching this week.</p>
                        <a href="<?php echo BASE_URL; ?>/contact" class="btn-primary mt-6 px-5 py-3">Plan a Visit</a>
                    </div>
                <?php endif; ?>

                <?php foreach ($latestSermons as $sermon): ?>
                    <article class="card">
                        <p class="meta-pill"><i class="fas fa-calendar"></i><?php echo Helpers::escape(Helpers::formatDate($sermon['sermon_date'], 'M d, Y')); ?></p>
                        <h3 class="mt-4 text-xl font-extrabold text-rccg-navy"><?php echo Helpers::escape($sermon['title']); ?></h3>
                        <p class="mt-2 text-gray-600"><?php echo Helpers::escape($sermon['preacher']); ?></p>
                        <a href="<?php echo BASE_URL; ?>/sermons/<?php echo Helpers::escape($sermon['slug']); ?>" class="mt-5 inline-flex font-extrabold text-rccg-red">Listen now</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-10 lg:grid-cols-2">
            <div>
                <div class="mb-6 flex items-end justify-between gap-4">
                    <div>
                        <p class="eyebrow">Gatherings</p>
                        <h2 class="section-title">Upcoming Events</h2>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/events" class="font-extrabold text-rccg-red">All events</a>
                </div>
                <div class="space-y-4">
                    <?php if (empty($upcomingEvents)): ?>
                        <div class="empty-state text-left">
                            <span class="empty-state-icon"><i class="fas fa-calendar-days"></i></span>
                            <h3 class="text-xl font-extrabold text-rccg-navy">Events are being prepared</h3>
                            <p class="mt-2 text-gray-600">Upcoming programs will be listed here. You can still join our regular worship and midweek services.</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($upcomingEvents as $event): ?>
                        <article class="card">
                            <p class="meta-pill"><i class="fas fa-calendar"></i><?php echo Helpers::escape(Helpers::formatDate($event['event_date'], 'M d, Y')); ?></p>
                            <h3 class="mt-4 text-xl font-extrabold text-rccg-navy"><?php echo Helpers::escape($event['title']); ?></h3>
                            <p class="mt-2 text-gray-600"><?php echo Helpers::escape($event['venue'] ?: 'Venue to be announced'); ?></p>
                            <a href="<?php echo BASE_URL; ?>/events/<?php echo Helpers::escape($event['slug']); ?>" class="mt-4 inline-flex font-extrabold text-rccg-red">View event</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <div class="mb-6 flex items-end justify-between gap-4">
                    <div>
                        <p class="eyebrow">Serve</p>
                        <h2 class="section-title">Ministries</h2>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/ministries" class="font-extrabold text-rccg-red">All ministries</a>
                </div>
                <div class="space-y-4">
                    <?php if (empty($ministries)): ?>
                        <div class="empty-state text-left">
                            <span class="empty-state-icon"><i class="fas fa-handshake-angle"></i></span>
                            <h3 class="text-xl font-extrabold text-rccg-navy">Ministry details are coming</h3>
                            <p class="mt-2 text-gray-600">Serving opportunities will appear here. Contact us if you are ready to get involved now.</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($ministries as $ministry): ?>
                        <article class="card">
                            <h3 class="text-xl font-extrabold text-rccg-navy"><?php echo Helpers::escape($ministry['name']); ?></h3>
                            <p class="mt-2 text-gray-600"><?php echo Helpers::escape($ministry['short_desc'] ?: 'Serve and grow with this ministry.'); ?></p>
                            <a href="<?php echo BASE_URL; ?>/ministries/<?php echo Helpers::escape($ministry['slug']); ?>" class="mt-4 inline-flex font-extrabold text-rccg-red">Learn more</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 lg:grid-cols-3">
            <article class="card lg:col-span-2">
                <p class="eyebrow">Prayer & Care</p>
                <h2 class="mt-3 font-display text-3xl font-bold text-rccg-navy">You are not alone</h2>
                <p class="mt-4 max-w-3xl text-gray-600">Share a prayer request, ask for pastoral care, or let the church family stand with you in faith.</p>
                <a href="<?php echo BASE_URL; ?>/prayer" class="btn-primary mt-6 px-5 py-3">Request Prayer</a>
            </article>
            <article class="card">
                <p class="eyebrow">Generosity</p>
                <h2 class="mt-3 text-2xl font-extrabold text-rccg-navy">Give securely online</h2>
                <p class="mt-3 text-gray-600">Support ministry, outreach, welfare, and mission through your giving.</p>
                <a href="<?php echo BASE_URL; ?>/give" class="mt-5 inline-flex font-extrabold text-rccg-red">Give now</a>
            </article>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 lg:grid-cols-[0.85fr_1.15fr] lg:items-stretch">
            <div class="card">
                <p class="eyebrow">Visit us</p>
                <h2 class="mt-3 font-display text-3xl font-bold text-rccg-navy">We are ready to welcome you</h2>
                <div class="mt-6 space-y-4 text-gray-600">
                    <p><i class="fas fa-location-dot mr-2 text-rccg-red"></i><?php echo Helpers::escape($settings['church_address'] ?? CHURCH_ADDRESS); ?></p>
                    <p><i class="fas fa-phone mr-2 text-rccg-red"></i><?php echo Helpers::escape($settings['church_phone'] ?? CHURCH_PHONE); ?></p>
                    <p><i class="fas fa-envelope mr-2 text-rccg-red"></i><?php echo Helpers::escape($settings['church_email'] ?? CHURCH_EMAIL); ?></p>
                </div>
                <a href="<?php echo BASE_URL; ?>/contact" class="btn-primary mt-6 px-5 py-3">Contact the Church</a>
            </div>
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-100 shadow-sm">
                <div class="flex h-full min-h-[22rem] items-center justify-center bg-[linear-gradient(135deg,#071427,#981331)] p-8 text-center text-white">
                    <div>
                        <i class="fas fa-location-dot text-5xl text-rccg-gold"></i>
                        <h3 class="mt-5 font-display text-3xl font-bold">Find us in Lagos</h3>
                        <p class="mx-auto mt-3 max-w-xl text-gray-200"><?php echo Helpers::escape($settings['church_address'] ?? CHURCH_ADDRESS); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="eyebrow text-rccg-gold">Welcome home</p>
            <h2 class="mx-auto mt-3 max-w-4xl font-display text-4xl font-bold md:text-5xl">Take your next step with Christ and with this church family</h2>
            <p class="mx-auto mt-5 max-w-2xl text-gray-200">Visit this week, become a member, request prayer, or connect with a ministry. We would love to walk with you.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="<?php echo BASE_URL; ?>/contact" class="btn-primary px-6 py-3">Plan a Visit</a>
                <a href="<?php echo BASE_URL; ?>/join" class="btn-outline px-6 py-3">Join the Church</a>
            </div>
        </div>
    </section>
</main>
