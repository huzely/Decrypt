<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

function admin_login(string $username, string $password): bool
{
    ensure_session();
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    if ($row && password_verify($password, $row['password_hash'])) {
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['admin_username'] = $row['username'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        return true;
    }
    return false;
}

function admin_require(): void
{
    ensure_session();
    if (empty($_SESSION['admin_id'])) {
        header('Location: /admin/login.php');
        exit;
    }
}

function admin_logout(): void
{
    ensure_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function admin_name(): string
{
    ensure_session();
    return (string)($_SESSION['admin_username'] ?? '');
}
