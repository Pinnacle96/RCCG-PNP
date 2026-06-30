<?php
/**
 * @var string $siteName
 * @var array $currentUser
 */
$path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/';
$basePath = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
if ($basePath !== '' && str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath)) ?: '/';
}
$navItems = [
    ['label' => 'Home', 'url' => '', 'match' => '/'],
    ['label' => 'About', 'url' => '/about', 'match' => '/about'],
    ['label' => 'Sermons', 'url' => '/sermons', 'match' => '/sermons'],
    ['label' => 'Events', 'url' => '/events', 'match' => '/events'],
    ['label' => 'Ministries', 'url' => '/ministries', 'match' => '/ministries'],
    ['label' => 'Prayer', 'url' => '/prayer', 'match' => '/prayer'],
];
$isActive = static function (string $match) use ($path): bool {
    return $match === '/' ? $path === '/' : str_starts_with($path, $match);
};
?>
<nav class="site-nav sticky top-0 z-50 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-20 items-center justify-between gap-4">
            <a href="<?php echo BASE_URL; ?>" class="flex min-w-0 items-center gap-3">
                <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="RCCG Prince and Princess Parish" class="h-16 w-auto">
            </a>

            <div class="hidden items-center gap-1 lg:flex">
                <?php foreach ($navItems as $item): ?>
                    <a href="<?php echo BASE_URL . $item['url']; ?>" class="nav-link <?php echo $isActive($item['match']) ? 'is-active' : ''; ?>">
                        <?php echo Helpers::escape($item['label']); ?>
                    </a>
                <?php endforeach; ?>
                <a href="<?php echo BASE_URL; ?>/give" class="nav-link <?php echo $isActive('/give') ? 'is-active' : ''; ?>">Give</a>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <?php if ($currentUser): ?>
                    <a href="<?php echo BASE_URL; ?>/<?php echo ($currentUser['role'] ?? '') === 'member' ? 'portal' : 'admin'; ?>" class="btn-secondary px-4 py-2">
                        <i class="fas fa-gauge-high"></i>
                        Dashboard
                    </a>
                    <a href="<?php echo BASE_URL; ?>/logout" class="nav-link" aria-label="Logout">
                        <i class="fas fa-arrow-right-from-bracket"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login" class="nav-link">Login</a>
                    <a href="<?php echo BASE_URL; ?>/register" class="btn-primary px-4 py-2">
                        <i class="fas fa-user-plus"></i>
                        Register
                    </a>
                <?php endif; ?>
            </div>

            <button id="mobile-menu-toggle" data-menu-toggle class="brand-mark md:hidden" type="button" aria-label="Open menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <div id="mobile-menu" data-mobile-menu class="hidden absolute left-0 right-0 top-full border-t border-gray-200 bg-white px-4 py-4 shadow-xl z-50 md:hidden">
        <div class="max-w-7xl mx-auto space-y-1">
            <?php foreach ($navItems as $item): ?>
                <a href="<?php echo BASE_URL . $item['url']; ?>" class="mobile-nav-link <?php echo $isActive($item['match']) ? 'is-active' : ''; ?>">
                    <?php echo Helpers::escape($item['label']); ?>
                </a>
            <?php endforeach; ?>
            <a href="<?php echo BASE_URL; ?>/give" class="mobile-nav-link <?php echo $isActive('/give') ? 'is-active' : ''; ?>">Give</a>
            <div class="mt-3 border-t border-gray-100 pt-3">
                <?php if ($currentUser): ?>
                    <a href="<?php echo BASE_URL; ?>/<?php echo ($currentUser['role'] ?? '') === 'member' ? 'portal' : 'admin'; ?>" class="mobile-nav-link">Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>/logout" class="mobile-nav-link">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login" class="mobile-nav-link">Login</a>
                    <a href="<?php echo BASE_URL; ?>/register" class="mobile-nav-link is-active">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
