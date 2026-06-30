<?php
/**
 * @var string $siteName
 * @var string $content
 * @var array $data
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Helpers::escape($data['title'] ?? 'Authentication'); ?> - <?php echo Helpers::escape($siteName); ?></title>
    <meta name="description" content="Login or register">
    <meta name="csrf-token" content="<?php echo $data['csrf'] ?? ''; ?>">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    
    <!-- Tailwind CDN for local development; replace with compiled CSS before production launch -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/custom.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-shell min-h-screen flex items-center justify-center p-4">
    <div class="auth-card max-w-md w-full">
        <div class="p-7 text-center">
            <div class="mx-auto mb-4 brand-mark"><i class="fas fa-church"></i></div>
            <h1 class="font-display text-2xl font-bold text-rccg-navy"><?php echo Helpers::escape($siteName); ?></h1>
            <p class="mt-1 text-sm font-bold uppercase tracking-wide text-rccg-red">Welcome Back</p>
        </div>
        
        <div class="px-8 pb-8">
            <?php require PARTIAL_PATH . 'flash-alerts.php'; ?>
            <?php echo $content; ?>
        </div>
        
        <div class="border-t border-gray-100 bg-gray-50 p-4 text-center text-sm text-gray-600">
            <p>&copy; <?php echo date('Y'); ?> <?php echo Helpers::escape($siteName); ?>. All rights reserved.</p>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>
</body>
</html>
