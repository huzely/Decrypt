<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/error_handler.php';

function db(): PDO
{
    static $pdo;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        if (APP_DEBUG) {
            throw $e;
        }
        file_put_contents(__DIR__ . '/../logs/app.log', '[' . date('c') . "] DB connect error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        render_error_page();
    }

    return $pdo;
}

function load_settings(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT * FROM settings WHERE id = 1 LIMIT 1');
    $settings = $stmt->fetch();
    if (!$settings) {
        $settings = [
            'ads_enabled' => 0,
            'ad_link' => '',
            'ad_title' => '',
            'ad_body' => '',
            'contact_link' => ''
        ];
    }
    return $settings;
}
