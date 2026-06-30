<?php

/** @var Router $router */

$router->get('/api/health', function () {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'API is reachable',
        'data' => [
            'app' => SITE_NAME,
            'time' => date('c'),
        ],
    ]);
});

/* ---------------------------------------------------------------------- */
/* Public API (SSOT §10)                                                   */
/* ---------------------------------------------------------------------- */
$router->get('/api/sermons/search', 'Api/PublicController@sermonSearch');
$router->get('/api/sermons/filter', 'Api/PublicController@sermonFilter');
$router->post('/api/sermons/view', 'Api/PublicController@sermonView');
$router->post('/api/sermons/download', 'Api/PublicController@sermonDownload');
$router->post('/api/prayer/submit', 'Api/PublicController@prayerSubmit');
$router->post('/api/prayer/praying', 'Api/PublicController@prayerPraying');
$router->post('/api/newsletter/subscribe', 'Api/PublicController@newsletterSubscribe');
$router->post('/api/contact/send', 'Api/PublicController@contactSend');
$router->get('/api/announcements', 'Api/PublicController@announcements');
$router->get('/api/livestream/status', 'Api/PublicController@livestreamStatus');
$router->get('/api/search', 'Api/PublicController@search');

/* ---------------------------------------------------------------------- */
/* Admin API (SSOT §10)                                                    */
/* ---------------------------------------------------------------------- */
$router->get('/api/admin/members/search', 'Api/AdminController@memberSearch');
$router->post('/api/admin/attendance/mark', 'Api/AdminController@attendanceMark');
$router->post('/api/admin/attendance/qr', 'Api/AdminController@attendanceQr');
$router->post('/api/admin/giving/record', 'Api/AdminController@givingRecord');
$router->get('/api/admin/dashboard/stats', 'Api/AdminController@dashboardStats');
$router->post('/api/admin/contact/reply', 'Api/AdminController@contactReply');
$router->get('/api/admin/notifications', 'Api/AdminController@notifications');
$router->post('/api/admin/notifications/read', 'Api/AdminController@notificationsRead');
