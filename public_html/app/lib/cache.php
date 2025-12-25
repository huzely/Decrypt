<?php
require_once __DIR__ . '/../config/config.php';

function cache_get(string $key, int $ttl = CACHE_TTL) {
    $file = CACHE_PATH . '/' . md5($key) . '.cache';
    if (!file_exists($file)) { return null; }
    if (filemtime($file) + $ttl < time()) { @unlink($file); return null; }
    return unserialize(file_get_contents($file));
}

function cache_set(string $key, $value): void {
    if (!is_dir(CACHE_PATH)) { mkdir(CACHE_PATH, 0775, true); }
    $file = CACHE_PATH . '/' . md5($key) . '.cache';
    file_put_contents($file, serialize($value));
}
