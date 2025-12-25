<?php
use App\Core\Security;

function base_url(string $path = ''): string
{
    $base = rtrim(App\Core\Config::get('app.base_url'), '/');
    return $base . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function csrf_token(): string
{
    return Security::csrfToken();
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!Security::verifyCsrf($token)) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }
}

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('~[^-a-z0-9]+~', '', $text);
    return $text ?: 'n-a';
}

function is_bot(array $botList): bool
{
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (!$ua) {
        return true;
    }
    foreach ($botList as $bot) {
        if ($bot && stripos($ua, $bot) !== false) {
            return true;
        }
    }
    return false;
}

function client_fingerprint(): string
{
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return hash('sha256', $ua . '|' . $ip);
}

function human_date(string $date): string
{
    return date('d/m/Y H:i', strtotime($date));
}
