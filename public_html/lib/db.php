<?php
$config = require __DIR__ . '/../config.php';

try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $config['DB_HOST'], $config['DB_NAME']);
    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (Throwable $e) {
    if (!is_dir(dirname($config['LOG_PATH']))) {
        @mkdir(dirname($config['LOG_PATH']), 0777, true);
    }
    error_log($e->getMessage());
    http_response_code(500);
    echo 'Không thể kết nối cơ sở dữ liệu.';
    exit;
}
