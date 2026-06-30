<?php

require_once dirname(__DIR__) . '/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    exit('This command must be run from the command line.');
}

$checks = [
    'database_tables' => [
        'users',
        'members',
        'site_settings',
        'login_attempts',
        'schema_migrations',
    ],
    'required_files' => [
        APP . '/routes/web.php',
        APP . '/routes/api.php',
        APP . '/Core/Router.php',
        APP . '/Core/Controller.php',
        APP . '/Core/Model.php',
        APP . '/Core/Auth.php',
        APP . '/Core/Settings.php',
        APP . '/Models/UserModel.php',
        APP . '/Models/MemberModel.php',
        APP . '/Models/SettingsModel.php',
    ],
];

$failed = false;

foreach ($checks['database_tables'] as $table) {
    $exists = \Database::fetchOne(
        'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? LIMIT 1',
        [DB_NAME, $table]
    );
    fwrite(STDOUT, ($exists ? '[ok] ' : '[fail] ') . 'table ' . $table . PHP_EOL);
    $failed = $failed || !$exists;
}

foreach ($checks['required_files'] as $file) {
    $exists = is_file($file);
    fwrite(STDOUT, ($exists ? '[ok] ' : '[fail] ') . str_replace(ROOT . '/', '', $file) . PHP_EOL);
    $failed = $failed || !$exists;
}

$adminCount = (int) \Database::fetchColumn("SELECT COUNT(*) FROM users WHERE role = 'super_admin'");
$settingCount = (int) \Database::fetchColumn('SELECT COUNT(*) FROM site_settings');
fwrite(STDOUT, ($adminCount > 0 ? '[ok] ' : '[fail] ') . 'super admin exists' . PHP_EOL);
fwrite(STDOUT, ($settingCount > 0 ? '[ok] ' : '[fail] ') . 'settings seeded' . PHP_EOL);

exit($failed || $adminCount === 0 || $settingCount === 0 ? 1 : 0);
