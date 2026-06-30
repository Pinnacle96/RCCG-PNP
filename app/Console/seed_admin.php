<?php

require_once dirname(__DIR__) . '/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    exit('This command must be run from the command line.');
}

function optionValue(array $argv, string $name, ?string $default = null): ?string {
    $prefix = '--' . $name . '=';
    foreach ($argv as $arg) {
        if (str_starts_with($arg, $prefix)) {
            return substr($arg, strlen($prefix));
        }
    }
    return $default;
}

$email = optionValue($argv, 'email', 'admin@rccgpp.local');
$password = optionValue($argv, 'password');
$generated = false;

if (!$password) {
    $password = bin2hex(random_bytes(8));
    $generated = true;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, 'Invalid email address.' . PHP_EOL);
    exit(1);
}

if (strlen($password) < 8) {
    fwrite(STDERR, 'Password must be at least 8 characters.' . PHP_EOL);
    exit(1);
}

$userModel = new \App\Models\UserModel();
$existing = $userModel->findByEmail($email);

if ($existing) {
    $userModel->update((int) $existing['id'], [
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => ROLE_SUPER_ADMIN,
        'is_active' => 1,
    ]);
    fwrite(STDOUT, 'Updated existing super admin: ' . $email . PHP_EOL);
} else {
    $userModel->create([
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => ROLE_SUPER_ADMIN,
        'is_active' => 1,
    ]);
    fwrite(STDOUT, 'Created super admin: ' . $email . PHP_EOL);
}

if ($generated) {
    fwrite(STDOUT, 'Generated password: ' . $password . PHP_EOL);
}
