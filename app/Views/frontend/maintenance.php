<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance - <?php echo Helpers::escape(Settings::get('site_name', SITE_NAME)); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/custom.css">
</head>
<body class="auth-shell min-h-screen flex items-center justify-center px-6 text-white">
    <div class="max-w-xl text-center">
        <div class="brand-mark mx-auto mb-6 text-2xl font-extrabold">
            <?php echo Helpers::escape(strtoupper(substr(Settings::get('site_name', SITE_NAME), 0, 1))); ?>
        </div>
        <p class="eyebrow text-rccg-gold">Maintenance</p>
        <h1 class="mt-3 font-display text-4xl font-extrabold md:text-6xl"><?php echo Helpers::escape(Settings::get('site_name', SITE_NAME)); ?></h1>
        <h2 class="mt-5 text-2xl font-extrabold text-rccg-gold">We will be back shortly</h2>
        <p class="mt-4 leading-8 text-gray-200"><?php echo Helpers::escape($message ?? 'We are currently performing maintenance. Please check back soon.'); ?></p>
    </div>
</body>
</html>
