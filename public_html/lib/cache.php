<?php
$config = require __DIR__ . '/../config.php';

function cache_get(string $key) {
    global $config;
    $path = $config['CACHE_PATH'] . '/' . md5($key) . '.cache';
    if (!file_exists($path)) return null;
    $data = json_decode(file_get_contents($path), true);
    if (!$data || $data['expires'] < time()) {
        @unlink($path);
        return null;
    }
    return $data['value'];
}

function cache_set(string $key, $value, ?int $ttl = null): void {
    global $config;
    if (!is_dir($config['CACHE_PATH'])) {
        @mkdir($config['CACHE_PATH'], 0777, true);
    }
    $path = $config['CACHE_PATH'] . '/' . md5($key) . '.cache';
    if ($value === null) {
        @unlink($path);
        return;
    }
    $ttl = $ttl ?? $config['CACHE_TTL'];
    file_put_contents($path, json_encode([
        'expires' => time() + $ttl,
        'value' => $value,
    ]));
}
