<?php
/**
 * Application bootstrap shared by the web front controller and CLI tools.
 */

if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}
if (!defined('APP')) {
    define('APP', __DIR__);
}
if (!defined('STORAGE')) {
    define('STORAGE', ROOT . '/storage');
}
if (!defined('LOG_PATH')) {
    define('LOG_PATH', ROOT . '/logs');
}

if (!is_dir(LOG_PATH)) {
    mkdir(LOG_PATH, 0777, true);
}

set_exception_handler(function (\Throwable $e): void {
    $logFile = LOG_PATH . '/php-errors.log';
    $msg = '[' . date('Y-m-d H:i:s') . '] EXCEPTION: ' . $e->getMessage() . "\n";
    $msg .= '  File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
    $msg .= "  Trace:\n" . $e->getTraceAsString() . "\n\n";
    error_log($msg, 3, $logFile);

    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, $e->getMessage() . PHP_EOL);
        return;
    }

    if (defined('DEBUG') && DEBUG) {
        echo '<h2>Error</h2><p><strong>' . htmlspecialchars($e->getMessage()) . '</strong> in <code>' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</code></p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        echo 'An error occurred. Please try again later.';
    }
});

spl_autoload_register(function (string $class): void {
    $coreDir = APP . '/Core/';
    $modelsDir = APP . '/Models/';
    $controllersDir = APP . '/Controllers/';
    $configDir = APP . '/Config/';

    if (is_file($coreDir . $class . '.php')) {
        require_once $coreDir . $class . '.php';
        return;
    }

    if (is_file($configDir . $class . '.php')) {
        require_once $configDir . $class . '.php';
        return;
    }

    if (is_file($modelsDir . $class . '.php')) {
        require_once $modelsDir . $class . '.php';
        return;
    }

    if (strpos($class, 'App\\Controllers\\') === 0) {
        $relative = substr($class, strlen('App\\Controllers\\'));
        $file = $controllersDir . str_replace('\\', '/', $relative) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
        return;
    }

    if (strpos($class, 'App\\Models\\') === 0) {
        $name = substr($class, strlen('App\\Models\\'));
        if (is_file($modelsDir . $name . '.php')) {
            require_once $modelsDir . $name . '.php';
        }
    }
});

require_once APP . '/Config/config.php';

if (is_file(APP . '/vendor/autoload.php')) {
    require_once APP . '/vendor/autoload.php';
}
