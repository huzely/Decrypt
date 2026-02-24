<?php
require_once __DIR__ . '/../config.php';

$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0775, true);
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

set_exception_handler(function ($ex) {
    $error = sprintf('[%s] Uncaught %s: %s in %s on line %d%s', date('c'), get_class($ex), $ex->getMessage(), $ex->getFile(), $ex->getLine(), PHP_EOL);
    if (APP_DEBUG) {
        echo '<pre>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</pre>';
        return;
    }
    file_put_contents(__DIR__ . '/../logs/app.log', $error, FILE_APPEND);
    render_error_page();
});

register_shutdown_function(function () {
    $last = error_get_last();
    if ($last && in_array($last['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        $error = sprintf('[%s] Fatal: %s in %s on line %d%s', date('c'), $last['message'], $last['file'], $last['line'], PHP_EOL);
        if (APP_DEBUG) {
            echo '<pre>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</pre>';
        } else {
            file_put_contents(__DIR__ . '/../logs/app.log', $error, FILE_APPEND);
        }
    }
});
