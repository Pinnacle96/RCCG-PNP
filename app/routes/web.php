<?php

/** @var Router $router */

$router->get('/', 'Frontend/HomeController@index');
$router->get('/about', 'Frontend/AboutController@index');
$router->get('/contact', 'Frontend/ContactController@index');
$router->post('/contact', 'Frontend/ContactController@send');
$router->get('/sermons', 'Frontend/SermonController@index');
$router->get('/sermons/series/{slug}', 'Frontend/SermonController@series');
$router->get('/sermons/{slug}', 'Frontend/SermonController@show');
$router->get('/events', 'Frontend/EventController@index');
$router->get('/events/{slug}', 'Frontend/EventController@show');
$router->post('/events/{slug}/register', 'Frontend/EventController@register');
$router->get('/ministries', 'Frontend/MinistryController@index');
$router->get('/ministries/{slug}', 'Frontend/MinistryController@show');
$router->get('/blog', 'Frontend/BlogController@index');
$router->get('/blog/{slug}', 'Frontend/BlogController@show');
$router->get('/gallery', 'Frontend/GalleryController@index');
$router->get('/gallery/{slug}', 'Frontend/GalleryController@album');
$router->get('/prayer', 'Frontend/PrayerController@index');
$router->post('/prayer', 'Frontend/PrayerController@submit');
$router->get('/join', 'Frontend/JoinController@index');
$router->post('/join', 'Frontend/JoinController@submit');
$router->get('/search', 'Frontend/SearchController@index');
$router->get('/give', 'Frontend/GiveController@index');
$router->post('/give/initiate', 'Frontend/GiveController@initiate');
$router->get('/give/verify', 'Frontend/GiveController@verify');
$router->get('/give/success', 'Frontend/GiveController@success');
$router->post('/api/give/webhook/paystack', 'Frontend/GiveController@webhook');
$router->get('/sitemap.xml', 'Frontend/SeoController@sitemap');
$router->get('/robots.txt', 'Frontend/SeoController@robots');

$router->get('/livestream', 'Frontend/HomeController@livestream');

$router->get('/login', 'Auth/LoginController@showLogin');
$router->post('/login', 'Auth/LoginController@login');
$router->get('/logout', 'Auth/LoginController@logout');
$router->get('/register', 'Auth/RegisterController@showRegister');
$router->post('/register', 'Auth/RegisterController@register');
$router->get('/forgot-password', 'Auth/PasswordController@showForgot');
$router->post('/forgot-password', 'Auth/PasswordController@sendReset');
$router->get('/reset-password/{token}', 'Auth/PasswordController@showReset');
$router->post('/reset-password', 'Auth/PasswordController@reset');

$router->get('/admin', 'Admin/DashboardController@index');
$router->get('/admin/members', 'Admin/MemberController@index');
$router->get('/admin/members/export', 'Admin/MemberController@export');
$router->get('/admin/members/add', 'Admin/MemberController@create');
$router->post('/admin/members', 'Admin/MemberController@store');
$router->get('/admin/members/edit/{id}', 'Admin/MemberController@edit');
$router->post('/admin/members/edit/{id}', 'Admin/MemberController@update');
$router->get('/admin/members/view/{id}', 'Admin/MemberController@viewMember');
$router->get('/admin/attendance', 'Admin/AttendanceController@index');
$router->get('/admin/attendance/add', 'Admin/AttendanceController@create');
$router->post('/admin/attendance', 'Admin/AttendanceController@store');
$router->get('/admin/attendance/view/{id}', 'Admin/AttendanceController@show');
$router->get('/admin/attendance/edit/{id}', 'Admin/AttendanceController@edit');
$router->post('/admin/attendance/edit/{id}', 'Admin/AttendanceController@update');
$router->post('/admin/attendance/mark/{id}', 'Admin/AttendanceController@mark');
$router->post('/admin/attendance/remove/{id}', 'Admin/AttendanceController@remove');
$router->get('/admin/attendance/reports', 'Admin/AttendanceController@reports');

$router->get('/admin/giving', 'Admin/GivingController@index');
$router->get('/admin/giving/record', 'Admin/GivingController@create');
$router->post('/admin/giving/record', 'Admin/GivingController@store');
$router->get('/admin/giving/reports', 'Admin/GivingController@reports');
$router->get('/admin/giving/export', 'Admin/GivingController@export');
$router->get('/admin/giving/statement/{id}', 'Admin/GivingController@statement');
$router->post('/admin/giving/verify/{id}', 'Admin/GivingController@verify');

$router->get('/admin/prayer', 'Admin/PrayerController@index');
$router->get('/admin/prayer/view/{id}', 'Admin/PrayerController@show');
$router->post('/admin/prayer/update/{id}', 'Admin/PrayerController@update');
$router->post('/admin/prayer/reply/{id}', 'Admin/PrayerController@reply');
$router->post('/admin/prayer/delete/{id}', 'Admin/PrayerController@delete');

$router->get('/admin/reports', 'Admin/ReportController@index');
$router->get('/admin/reports/export', 'Admin/ReportController@export');

$router->get('/admin/users', 'Admin/UserController@index');
$router->get('/admin/users/add', 'Admin/UserController@create');
$router->post('/admin/users', 'Admin/UserController@store');
$router->get('/admin/users/edit/{id}', 'Admin/UserController@edit');
$router->post('/admin/users/edit/{id}', 'Admin/UserController@update');
$router->post('/admin/users/delete/{id}', 'Admin/UserController@delete');

$router->get('/admin/settings', 'Admin/SettingsController@index');
$router->post('/admin/settings', 'Admin/SettingsController@save');

$router->get('/admin/communications', 'Admin/CommunicationsController@index');
$router->post('/admin/communications/send', 'Admin/CommunicationsController@send');
$router->post('/admin/communications/sms', 'Admin/CommunicationsController@sendSms');

$router->get('/admin/audit-log', 'Admin/AuditController@index');

$adminCmsModules = ['sermons', 'sermon-series', 'events', 'event-registrations', 'ministries', 'cell-groups', 'blog', 'gallery-albums', 'gallery', 'announcements', 'contacts'];
foreach ($adminCmsModules as $adminModule) {
    $router->get('/admin/' . $adminModule, function () use ($adminModule) {
        $_GET['module'] = $adminModule;
        (new \App\Controllers\Admin\CmsController())->index();
    });
    $router->get('/admin/' . $adminModule . '/add', function () use ($adminModule) {
        $_GET['module'] = $adminModule;
        (new \App\Controllers\Admin\CmsController())->create();
    });
    $router->post('/admin/' . $adminModule, function () use ($adminModule) {
        $_GET['module'] = $adminModule;
        (new \App\Controllers\Admin\CmsController())->store();
    });
    $router->get('/admin/' . $adminModule . '/edit/{id}', function () use ($adminModule) {
        $_GET['module'] = $adminModule;
        (new \App\Controllers\Admin\CmsController())->edit();
    });
    $router->post('/admin/' . $adminModule . '/edit/{id}', function () use ($adminModule) {
        $_GET['module'] = $adminModule;
        (new \App\Controllers\Admin\CmsController())->update();
    });
    $router->post('/admin/' . $adminModule . '/delete/{id}', function () use ($adminModule) {
        $_GET['module'] = $adminModule;
        (new \App\Controllers\Admin\CmsController())->delete();
    });
}

$router->get('/portal', 'Member/DashboardController@index');
$router->get('/portal/profile', 'Member/ProfileController@show');
$router->post('/portal/profile', 'Member/ProfileController@update');
$router->get('/portal/giving', 'Member/GivingController@index');
$router->get('/portal/giving/statement', 'Member/GivingController@statement');
$router->get('/portal/attendance', 'Member/AttendanceController@index');
$router->get('/portal/cellgroup', 'Member/CellGroupController@index');
$router->get('/portal/ministry', 'Member/MinistryController@index');

$router->setNotFound('frontend.404');
