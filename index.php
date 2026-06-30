<?php
/**
 * Front Controller - Single Entry Point
 * All requests flow through here via .htaccess rewrite.
 */

require_once __DIR__ . '/app/bootstrap.php';

// Maintenance mode — public site is taken offline, but admin staff and the
// /admin + /login routes stay reachable so the toggle can be switched back off.
if (Settings::get('maintenance_mode') === '1') {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $isAdminArea = (bool) preg_match('#/(admin|login|logout)(/|$|\?)#', $uri);
    $isStaff = Auth::check() && in_array(Auth::role(), [ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PASTOR, ROLE_DEACON], true);
    if (!$isAdminArea && !$isStaff) {
        http_response_code(503);
        (new View())->render('frontend.maintenance', [
            'message' => Settings::get('maintenance_message', 'We are currently performing maintenance. Please check back soon.'),
        ]);
        exit;
    }
}

$router = new Router();
require_once APP . '/routes/web.php';
require_once APP . '/routes/api.php';

$router->dispatch();
