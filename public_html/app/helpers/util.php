<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

function slugify(string $text): string
{
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    return $text ?: 'n-a';
}

function e($value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function asset(string $path): string
{
    global $config;
    return rtrim($config['base_url'], '/') . '/assets/' . ltrim($path, '/');
}

function base_url(string $path = ''): string
{
    global $config;
    return rtrim($config['base_url'], '/') . '/' . ltrim($path, '/');
}

function is_logged_in(): bool
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /admin/index.php?login=1');
        exit;
    }
}

function cache_path(string $key): string
{
    global $config;
    $safeKey = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
    return $config['cache']['path'] . '/' . $safeKey . '.cache.php';
}

function cache_get(string $key, int $ttl)
{
    $file = cache_path($key);
    if (file_exists($file) && (time() - filemtime($file) < $ttl)) {
        return unserialize(file_get_contents($file));
    }
    return null;
}

function cache_set(string $key, $data): void
{
    global $config;
    if (!is_dir($config['cache']['path'])) {
        mkdir($config['cache']['path'], 0777, true);
    }
    file_put_contents(cache_path($key), serialize($data));
}

function paginate(int $total, int $perPage, int $page): array
{
    $pages = (int)ceil($total / $perPage);
    $page = max(1, min($page, $pages));
    $offset = ($page - 1) * $perPage;
    return [$offset, $perPage, $page, $pages];
}

function session_token(): string
{
    return $_SESSION['csrf_token'] ?? '';
}
