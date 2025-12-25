<?php
session_start();
require_once __DIR__ . '/../app/lib/auth.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../app/lib/csrf.php';
    csrf_verify();
    if (admin_login($_POST['username'], $_POST['password'])) {
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
        exit;
    }
    $message = 'Sai tài khoản hoặc mật khẩu';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đăng nhập</title><link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css"></head>
<body class="login-page">
    <form method="post" class="login-form">
        <h2>Admin Login</h2>
        <?php if ($message): ?><div class="alert"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>
