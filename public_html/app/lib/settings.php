<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/cache.php';

function get_settings(): array
{
    global $config;
    $cacheKey = 'site_settings';
    $cached = cache_get($cacheKey, $config['CACHE_TTL']['settings']);
    if ($cached) {
        return $cached;
    }
    $pdo = DB::conn();
    $stmt = $pdo->query('SELECT `key`, `value` FROM site_settings');
    $settings = [];
    foreach ($stmt->fetchAll() as $row) {
        $settings[$row['key']] = $row['value'];
    }
    cache_set($cacheKey, $settings);
    return $settings;
}

function save_settings(array $data): void
{
    global $config;
    $pdo = DB::conn();
    $stmt = $pdo->prepare('INSERT INTO site_settings (`key`, `value`) VALUES (:k, :v) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');
    foreach ($data as $k => $v) {
        $stmt->execute([':k' => $k, ':v' => $v]);
    }
    cache_set('site_settings', $data);
}
