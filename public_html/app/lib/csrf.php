<?php
$config = require __DIR__ . '/../config/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $secure,
    ]);
    session_name('news_session');
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

function csrf_token(): string
{
    return $_SESSION['csrf_token'] ?? '';
}

function verify_csrf(string $token): bool
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
