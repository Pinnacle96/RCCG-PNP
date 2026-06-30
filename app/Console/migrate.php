<?php

require_once dirname(__DIR__) . '/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    exit('This command must be run from the command line.');
}

function cliLine(string $message = ''): void {
    fwrite(STDOUT, $message . PHP_EOL);
}

function mysqlIdentifier(string $name): string {
    return '`' . str_replace('`', '``', $name) . '`';
}

$serverDsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$server = new PDO($serverDsn, DB_USER, DB_PASS, $options);
$server->exec('CREATE DATABASE IF NOT EXISTS ' . mysqlIdentifier(DB_NAME) . ' CHARACTER SET ' . DB_CHARSET . ' COLLATE utf8mb4_unicode_ci');

$pdo = Database::pdo();
$pdo->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) UNIQUE NOT NULL,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$files = glob(MIGRATION_PATH . '/*.sql') ?: [];
sort($files, SORT_NATURAL);

if (!$files) {
    cliLine('No migration files found.');
    exit(0);
}

foreach ($files as $file) {
    $migration = basename($file);
    $alreadyApplied = Database::fetchOne('SELECT id FROM schema_migrations WHERE migration = ? LIMIT 1', [$migration]);
    if ($alreadyApplied) {
        cliLine('Skipped: ' . $migration);
        continue;
    }

    $sql = file_get_contents($file);
    if ($sql === false || trim($sql) === '') {
        cliLine('Skipped empty migration: ' . $migration);
        continue;
    }

    $pdo->exec($sql);
    Database::insert('schema_migrations', ['migration' => $migration]);

    cliLine('Applied: ' . $migration);
}

cliLine('Migrations complete for database ' . DB_NAME . '.');
