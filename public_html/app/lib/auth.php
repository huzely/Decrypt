<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

function admin_log(string $action): void
{
    if (empty($_SESSION['admin_id'])) {
        return;
    }
    $pdo = DB::conn();
    $stmt = $pdo->prepare('INSERT INTO admin_logs (admin_id, action) VALUES (:aid, :action)');
    $stmt->execute([':aid' => $_SESSION['admin_id'], ':action' => $action]);
}

function is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function login(string $username, string $password): bool
{
    $pdo = DB::conn();
    $stmt = $pdo->prepare('SELECT id, password_hash, failed_attempts, locked_until FROM admins WHERE username = :u LIMIT 1');
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch();
    if ($user && !empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
        return false;
    }
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $user['id'];
        admin_log('login');
        $pdo->prepare('UPDATE admins SET failed_attempts = 0, locked_until = NULL WHERE id = :id')->execute([':id' => $user['id']]);
        return true;
    }
    if ($user) {
        $attempts = (int)$user['failed_attempts'] + 1;
        $lockMinutes = setting('login_lock_minutes', 10);
        $update = $pdo->prepare('UPDATE admins SET failed_attempts = :a, locked_until = CASE WHEN :a >= :max THEN DATE_ADD(NOW(), INTERVAL :lockm MINUTE) ELSE NULL END WHERE id = :id');
        $update->execute([':a' => $attempts, ':max' => (int)setting('login_max_attempts', 5), ':lockm' => (int)$lockMinutes, ':id' => $user['id']]);
    }
    return false;
}

function logout(): void
{
    admin_log('logout');
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
