<?php
require_once __DIR__ . '/../config.php';

if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0775, true);
}

function render_error_page(): void
{
    http_response_code(500);
    include __DIR__ . '/../error.php';
    exit;
}

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    $error = sprintf('[%s] %s in %s on line %d%s', date('c'), $message, $file, $line, PHP_EOL);

    if (APP_DEBUG) {
        echo '<pre>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</pre>';
        return true;
    }

    file_put_contents(__DIR__ . '/../logs/app.log', $error, FILE_APPEND);
    render_error_page();
    return true;
});

set_exception_handler(function ($exception) {
    $error = sprintf('[%s] Uncaught exception %s: %s in %s on line %d%s',
        date('c'), get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine(), PHP_EOL);

    if (APP_DEBUG) {
        echo '<pre>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</pre>';
        return;
    }

    file_put_contents(__DIR__ . '/../logs/app.log', $error, FILE_APPEND);
    render_error_page();
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        $message = sprintf('[%s] Fatal error: %s in %s on line %d%s', date('c'), $error['message'], $error['file'], $error['line'], PHP_EOL);
        if (APP_DEBUG) {
            echo '<pre>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</pre>';
        } else {
            file_put_contents(__DIR__ . '/../logs/app.log', $message, FILE_APPEND);
        }
    }
});
