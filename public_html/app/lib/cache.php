<?php
$config = require __DIR__ . '/../config/config.php';

function cache_path(string $key): string
{
    global $config;
    $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
    return rtrim($config['CACHE_PATH'], '/') . '/' . $safe . '.cache';
}

function cache_get(string $key, int $ttl)
{
    $file = cache_path($key);
    if (!file_exists($file)) {
        return null;
    }
    if ((time() - filemtime($file)) > $ttl) {
        return null;
    }
    $data = file_get_contents($file);
    return $data === false ? null : unserialize($data);
}

function cache_set(string $key, $value): void
{
    global $config;
    if (!is_dir($config['CACHE_PATH'])) {
        mkdir($config['CACHE_PATH'], 0777, true);
    }
    file_put_contents(cache_path($key), serialize($value), LOCK_EX);
}
