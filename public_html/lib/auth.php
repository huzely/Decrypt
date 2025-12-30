<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

if (!function_exists('ensure_session')) {
    function ensure_session(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}

function login_admin(string $username, string $password): bool
{
    ensure_session();
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        regenerate_csrf_token();
        return true;
    }
    return false;
}

function require_admin(): void
{
    ensure_session();
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /admin/login.php');
        exit;
    }
}

function logout_admin(): void
{
    ensure_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function current_admin_username(): string
{
    ensure_session();
    return $_SESSION['admin_username'] ?? '';
}
