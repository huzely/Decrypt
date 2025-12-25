<?php
$config = require __DIR__ . '/../../config.php';

if (!headers_sent()) {
    session_name($config['app']['session_name']);
    session_start();
}

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Simple helper autoload for global functions
require_once __DIR__ . '/../Helpers/helpers.php';

App\Core\Config::set($config);
App\Core\Security::setCsrfKey($config['security']['csrf_key']);
