<?php
$config = require __DIR__ . '/../config.php';

function setup_error_handler(array $config): void {
    ini_set('display_errors', $config['APP_DEBUG'] ? '1' : '0');
    ini_set('log_errors', '1');
    ini_set('error_log', $config['LOG_PATH']);

    set_exception_handler(function ($e) use ($config) {
        handle_error_page($e, $config);
    });

    set_error_handler(function ($severity, $message, $file, $line) use ($config) {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        handle_error_page(new ErrorException($message, 0, $severity, $file, $line), $config);
        return true;
    });
}

function handle_error_page(Throwable $e, array $config): void {
    if ($config['APP_DEBUG']) {
        http_response_code(500);
        echo '<h1>Lỗi hệ thống</h1>';
        echo '<pre>' . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
        return;
    }

    if (!is_dir(dirname($config['LOG_PATH']))) {
        @mkdir(dirname($config['LOG_PATH']), 0777, true);
    }
    error_log($e->getMessage());
    http_response_code(500);
    include __DIR__ . '/../error.php';
}

setup_error_handler($config);
