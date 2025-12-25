<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

function admin_login(string $username, string $password): bool {
    $stmt = db()->prepare('SELECT * FROM admins WHERE username = :u LIMIT 1');
    $stmt->execute([':u' => $username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['username'];
        return true;
    }
    return false;
}

function admin_require_login(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

function admin_logout(): void {
    session_destroy();
}
