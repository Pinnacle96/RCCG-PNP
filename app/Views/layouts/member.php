<?php
/**
 * @var string $siteName
 * @var string $content
 * @var array $data
 */
$path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/portal';
$basePath = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
if ($basePath !== '' && str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath)) ?: '/portal';
}
$memberItems = [
    ['Dashboard', '/portal', 'fa-gauge-high'],
    ['My Profile', '/portal/profile', 'fa-user'],
    ['My Giving', '/portal/giving', 'fa-credit-card'],
    ['My Attendance', '/portal/attendance', 'fa-calendar-check'],
    ['My Cell Group', '/portal/cellgroup', 'fa-people-group'],
    ['My Ministry', '/portal/ministry', 'fa-handshake-angle'],
];
$isActive = static function (string $url) use ($path): bool {
    return $url === '/portal' ? $path === '/portal' : str_starts_with($path, $url);
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Helpers::escape($data['title'] ?? 'Member Portal'); ?> - <?php echo Helpers::escape($siteName); ?></title>
    <meta name="description" content="Member Portal">
    <meta name="csrf-token" content="<?php echo $data['csrf'] ?? ''; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="dashboard-body text-gray-900">
    <div class="dashboard-shell flex h-screen overflow-hidden">
        <aside class="dashboard-sidebar hidden w-72 shrink-0 flex-col md:flex">
            <div class="flex items-center gap-3 border-b border-gray-100 p-5">
                <span class="dashboard-logo"><i class="fas fa-church"></i></span>
                <div class="min-w-0">
                    <h1 class="truncate font-display text-xl font-bold text-rccg-navy"><?php echo Helpers::escape($siteName); ?></h1>
                    <p class="text-xs font-extrabold uppercase tracking-wide text-rccg-red">Member Portal</p>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <p class="dashboard-nav-label">My Church Life</p>
                <?php foreach ($memberItems as $item): ?>
                    <a href="<?php echo BASE_URL . $item[1]; ?>" class="dashboard-link <?php echo $isActive($item[1]) ? 'is-active' : ''; ?>">
                        <i class="fas <?php echo $item[2]; ?>"></i>
                        <span><?php echo Helpers::escape($item[0]); ?></span>
                    </a>
                <?php endforeach; ?>
                <p class="dashboard-nav-label">Quick Actions</p>
                <a href="<?php echo BASE_URL; ?>/give" class="dashboard-link">
                    <i class="fas fa-heart"></i>
                    <span>Give Online</span>
                </a>
                <a href="<?php echo BASE_URL; ?>/prayer" class="dashboard-link">
                    <i class="fas fa-hands-praying"></i>
                    <span>Prayer Request</span>
                </a>
            </nav>

            <div class="border-t border-gray-100 p-4">
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="truncate text-sm font-extrabold text-rccg-navy"><?php echo Helpers::escape($data['currentUser']['email'] ?? 'Member'); ?></p>
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Member Account</p>
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
            <header class="dashboard-topbar flex h-20 items-center justify-between px-4 sm:px-6">
                <div class="flex min-w-0 items-center gap-4">
                    <button id="mobile-menu-toggle" class="brand-mark md:hidden" type="button" aria-label="Open menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="min-w-0">
                        <p class="text-xs font-extrabold uppercase tracking-wide text-rccg-red">Portal</p>
                        <h2 class="truncate text-xl font-extrabold text-rccg-navy md:text-2xl">
                            <?php echo Helpers::escape($data['page_title'] ?? 'Dashboard'); ?>
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="<?php echo BASE_URL; ?>" class="btn-secondary hidden px-4 py-2 sm:inline-flex">
                        <i class="fas fa-globe"></i>
                        Site
                    </a>
                    <a href="<?php echo BASE_URL; ?>/logout" class="brand-mark" aria-label="Logout">
                        <i class="fas fa-arrow-right-from-bracket"></i>
                    </a>
                </div>
            </header>

            <main class="dashboard-content flex-1 overflow-y-auto">
                <?php require PARTIAL_PATH . 'flash-alerts.php'; ?>
                <?php echo $content; ?>
            </main>
        </div>
    </div>

    <div id="mobile-menu" class="fixed inset-0 z-40 hidden bg-black/50 md:hidden">
        <div class="dashboard-sidebar h-full w-80 max-w-[86vw] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-gray-100 p-4">
                <div class="flex items-center gap-3">
                    <span class="dashboard-logo"><i class="fas fa-church"></i></span>
                    <span class="font-display text-lg font-bold text-rccg-navy">Portal</span>
                </div>
                <button id="mobile-menu-close" class="brand-mark" type="button" aria-label="Close menu"><i class="fas fa-xmark"></i></button>
            </div>
            <nav class="py-4">
                <p class="dashboard-nav-label">My Church Life</p>
                <?php foreach ($memberItems as $item): ?>
                    <a href="<?php echo BASE_URL . $item[1]; ?>" class="dashboard-link <?php echo $isActive($item[1]) ? 'is-active' : ''; ?>">
                        <i class="fas <?php echo $item[2]; ?>"></i>
                        <span><?php echo Helpers::escape($item[0]); ?></span>
                    </a>
                <?php endforeach; ?>
                <p class="dashboard-nav-label">Quick Actions</p>
                <a href="<?php echo BASE_URL; ?>/give" class="dashboard-link"><i class="fas fa-heart"></i><span>Give Online</span></a>
                <a href="<?php echo BASE_URL; ?>/prayer" class="dashboard-link"><i class="fas fa-hands-praying"></i><span>Prayer Request</span></a>
            </nav>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/member.js"></script>
</body>
</html>
