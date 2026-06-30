<?php
/**
 * @var string $siteName
 * @var array $settings
 */
?>
<footer class="site-footer text-white">
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-10 lg:grid-cols-[1.35fr_0.8fr_0.9fr_1fr]">
            <div>
                <div class="flex items-center gap-3">
                    <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="RCCG Prince and Princess Parish" class="h-20 w-auto">
                </div>
                <p class="mt-5 max-w-md text-sm leading-7 text-gray-300">
                    A vibrant, spirit-filled family devoted to worship, discipleship, service, and helping people take their next step with Christ.
                </p>
                <div class="mt-6 flex gap-3">
                    <a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-extrabold uppercase tracking-wide text-rccg-gold">Explore</h4>
                <ul class="space-y-3 text-sm text-gray-300">
                    <li><a href="<?php echo BASE_URL; ?>" class="hover:text-white">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/about" class="hover:text-white">About Us</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/sermons" class="hover:text-white">Sermons</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/events" class="hover:text-white">Events</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/ministries" class="hover:text-white">Ministries</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-extrabold uppercase tracking-wide text-rccg-gold">Service Times</h4>
                <ul class="space-y-3 text-sm text-gray-300">
                    <li><?php echo Helpers::escape($settings['service_sunday_first'] ?? SERVICE_SUNDAY_FIRST); ?></li>
                    <?php if (!empty($settings['service_sunday_second'] ?? SERVICE_SUNDAY_SECOND)): ?>
                        <li><?php echo Helpers::escape($settings['service_sunday_second'] ?? SERVICE_SUNDAY_SECOND); ?></li>
                    <?php endif; ?>
                    <li><?php echo Helpers::escape($settings['service_wednesday'] ?? SERVICE_WEDNESDAY); ?></li>
                    <li><?php echo Helpers::escape($settings['service_friday'] ?? SERVICE_FRIDAY); ?></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-extrabold uppercase tracking-wide text-rccg-gold">Visit</h4>
                <ul class="space-y-4 text-sm text-gray-300">
                    <li class="flex gap-3"><i class="fas fa-location-dot mt-1 text-rccg-gold"></i><span><?php echo Helpers::escape($settings['church_address'] ?? CHURCH_ADDRESS); ?></span></li>
                    <li class="flex gap-3"><i class="fas fa-phone mt-1 text-rccg-gold"></i><span><?php echo Helpers::escape($settings['church_phone'] ?? CHURCH_PHONE); ?></span></li>
                    <li class="flex gap-3"><i class="fas fa-envelope mt-1 text-rccg-gold"></i><span><?php echo Helpers::escape($settings['church_email'] ?? CHURCH_EMAIL); ?></span></li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-6 text-sm text-gray-400 md:flex-row md:items-center md:justify-between">
            <p>&copy; <?php echo date('Y'); ?> <?php echo Helpers::escape($siteName); ?>. All rights reserved.</p>
            <div class="flex gap-5">
                <a href="<?php echo BASE_URL; ?>/prayer" class="hover:text-white">Prayer Request</a>
                <a href="<?php echo BASE_URL; ?>/join" class="hover:text-white">Join Us</a>
                <a href="<?php echo BASE_URL; ?>/give" class="hover:text-white">Give Online</a>
            </div>
        </div>
    </div>
</footer>
