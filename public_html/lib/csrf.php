<?php
function ensure_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function csrf_token(): string
{
    ensure_session();
    if (empty($_SESSION['csrf_token'])) {
        regenerate_csrf_token();
    }
    return $_SESSION['csrf_token'];
}

function regenerate_csrf_token(): void
{
    ensure_session();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

function verify_csrf_token(?string $token): bool
{
    ensure_session();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}
