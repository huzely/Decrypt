<?php
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/slugify.php';
$config = require __DIR__ . '/../config/config.php';

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function base_url(string $path = ''): string
{
    global $config;
    return rtrim($config['BASE_URL'], '/') . '/' . ltrim($path, '/');
}

function current_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    return $scheme . '://' . $host . $uri;
}

function sanitize_text(string $text): string
{
    return trim(strip_tags($text));
}
