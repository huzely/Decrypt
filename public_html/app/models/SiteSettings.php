<?php
require_once __DIR__ . '/../helpers/db.php';
require_once __DIR__ . '/../helpers/util.php';

class SiteSettings
{
    public static function get(): array
    {
        $cacheKey = 'site_settings';
        $cached = cache_get($cacheKey, 300);
        if ($cached) {
            return $cached;
        }
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT `key`, `value` FROM site_settings');
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['key']] = $row['value'];
        }
        cache_set($cacheKey, $settings);
        return $settings;
    }

    public static function set(array $data): void
    {
        $pdo = Database::getConnection();
        foreach ($data as $key => $value) {
            $stmt = $pdo->prepare('INSERT INTO site_settings (`key`, `value`) VALUES (:key, :value) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
        cache_set('site_settings', $data);
    }
}
