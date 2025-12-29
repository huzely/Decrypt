<?php
require_once __DIR__ . '/cache.php';

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function current_url(string $path = ''): string {
    $config = require __DIR__ . '/../config.php';
    return rtrim($config['BASE_URL'], '/') . '/' . ltrim($path, '/');
}

function get_post_param(string $key, $default = null) {
    return $_POST[$key] ?? $default;
}

function get_get_param(string $key, $default = null) {
    return $_GET[$key] ?? $default;
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function is_post(): bool {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function random_token(int $length = 32): string {
    return bin2hex(random_bytes($length));
}

function load_settings(PDO $pdo): array {
    $cacheKey = 'site_settings';
    $settings = cache_get($cacheKey);
    if ($settings) return $settings;
    $stmt = $pdo->query('SELECT * FROM site_settings LIMIT 1');
    $settings = $stmt->fetch() ?: [];
    cache_set($cacheKey, $settings, 300);
    return $settings;
}
