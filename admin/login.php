<?php
require_once __DIR__ . '/../config.php';

if (isAdmin()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password']) && $user['role'] === 'admin') {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Sai thông tin đăng nhập';
}
?>
<!DOCTYPE html>
<html lang="vi"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#000;color:#ddd;display:flex;align-items:center;justify-content:center;min-height:100vh}.box{width:100%;max-width:420px;background:#111;border:1px solid #222;border-radius:12px;padding:24px}</style>
</head><body>
<div class="box"><h3 class="text-center text-danger mb-3">AVSTube Admin</h3>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post"><input class="form-control bg-dark text-light border-secondary mb-2" name="username" required placeholder="Username">
<input type="password" class="form-control bg-dark text-light border-secondary mb-3" name="password" required placeholder="Password">
<button class="btn btn-danger w-100">Đăng nhập</button></form></div>
</body></html>
