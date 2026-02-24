<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/error_handler.php';

ensure_session();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (admin_login($username, $password)) {
        header('Location: /admin/panel.php');
        exit;
    }
    $error = 'Sai tài khoản hoặc mật khẩu';
}
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h2>Đăng nhập admin</h2>
        <?php if ($error): ?><p class="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
        <form method="post" autocomplete="off">
            <label>Tên đăng nhập</label>
            <input name="username" required>
            <label>Mật khẩu</label>
            <input type="password" name="password" required>
            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</div>
</body>
</html>
