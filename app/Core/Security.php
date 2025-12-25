<?php
namespace App\Core;

class Security
{
    private static string $csrfKey = 'csrf_token';

    public static function setCsrfKey(string $key): void
    {
        self::$csrfKey = $key ?: 'csrf_token';
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }
        return hash_hmac('sha256', $_SESSION['csrf_token'], self::$csrfKey);
    }

    public static function verifyCsrf(string $token): bool
    {
        return hash_equals(self::csrfToken(), $token);
    }

    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeFilename(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9-_\.]/', '', $name);
    }
}
