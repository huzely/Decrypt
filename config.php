<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'avstube');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function isAdmin(): bool
{
    return isset($_SESSION['admin_id']);
}

function adminOnly(): void
{
    if (!isAdmin()) {
        header('Location: login.php');
        exit;
    }
}

function setting(PDO $pdo, string $key, string $default = ''): string
{
    $stmt = $pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
    $stmt->execute(['key' => $key]);
    $result = $stmt->fetchColumn();

    return $result !== false ? (string) $result : $default;
}
