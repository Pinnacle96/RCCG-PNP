<?php
/**
 * @var string $siteName
 * @var string $siteTagline
 * @var string $content
 * @var array $data
 * @var array $settings
 */
$requestPath = strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/';
$basePath = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
    $requestPath = substr($requestPath, strlen($basePath)) ?: '/';
}
$canonicalUrl = $data['canonical'] ?? rtrim(BASE_URL, '/') . '/' . ltrim($requestPath, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Helpers::escape($data['title'] ?? $siteName); ?></title>
    <meta name="description" content="<?php echo Helpers::escape($data['description'] ?? $siteTagline); ?>">
    <meta name="csrf-token" content="<?php echo Helpers::escape($data['csrf'] ?? ''); ?>">
    <link rel="canonical" href="<?php echo Helpers::escape($canonicalUrl); ?>">
    <meta property="og:title" content="<?php echo Helpers::escape($data['title'] ?? $siteName); ?>">
    <meta property="og:description" content="<?php echo Helpers::escape($data['description'] ?? $siteTagline); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo Helpers::escape($canonicalUrl); ?>">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    
    <!-- Tailwind CDN for local development; replace with compiled CSS before production launch -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/custom.css">
    
    <!-- Font Awesome (fallback) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="public-site text-gray-900">
    <?php require PARTIAL_PATH . 'nav.php'; ?>
    <?php require PARTIAL_PATH . 'flash-alerts.php'; ?>
    <?php echo $content; ?>
    <?php require PARTIAL_PATH . 'footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>

    <?php if (\Recaptcha::siteConfigured()): ?>
    <!-- reCAPTCHA v3 — only loaded when a site key is configured -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo Helpers::escape(RECAPTCHA_SITE_KEY); ?>"></script>
    <script>
    (function () {
        var siteKey = '<?php echo Helpers::escape(RECAPTCHA_SITE_KEY); ?>';
        function refreshTokens() {
            if (!window.grecaptcha || !grecaptcha.execute) { return; }
            grecaptcha.ready(function () {
                grecaptcha.execute(siteKey, { action: 'submit' }).then(function (token) {
                    document.querySelectorAll('form[method="post"], form[method="POST"]').forEach(function (form) {
                        var field = form.querySelector('input[name="recaptcha_token"]');
                        if (!field) {
                            field = document.createElement('input');
                            field.type = 'hidden';
                            field.name = 'recaptcha_token';
                            form.appendChild(field);
                        }
                        field.value = token;
                    });
                });
            });
        }
        refreshTokens();
        setInterval(refreshTokens, 100000); // v3 tokens expire after ~2 min
    })();
    </script>
    <?php endif; ?>
</body>
</html>
