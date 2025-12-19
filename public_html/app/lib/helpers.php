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

function asset_url(string $path): string
{
    return base_url('assets/' . ltrim($path, '/'));
}

function article_url(array $post): string
{
    $slugMode = setting('slug_mode', 'slug');
    if ($slugMode === 'slug-id') {
        return '/' . $post['slug'] . '-' . $post['id'];
    }
    return '/' . $post['slug'];
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

function is_bot_user_agent(): bool
{
    $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    $bots = setting('bot_list', 'bot|crawl|spider|curl');
    return (bool)preg_match('/(' . $bots . ')/i', $ua);
}

function telegram_notify(string $message): void
{
    global $config;
    $token = setting('telegram_token', $config['TELEGRAM_BOT_TOKEN'] ?? '');
    $chatId = setting('telegram_chat_id', $config['TELEGRAM_CHAT_ID'] ?? '');
    if (!$token || !$chatId) return;
    $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
    $payload = ['chat_id' => $chatId, 'text' => $message, 'parse_mode' => 'HTML'];
    @file_get_contents($url, false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode($payload),
            'timeout' => 4,
        ]
    ]));
}
