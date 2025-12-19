<?php
require_once __DIR__ . '/../app/lib/auth.php';
require_once __DIR__ . '/../app/lib/helpers.php';
require_once __DIR__ . '/../app/lib/settings.php';
$settings = get_settings();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $error = 'Phiên đăng nhập không hợp lệ';
    } else {
        $username = sanitize_text($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (login($username, $password)) {
            header('Location: /admin/dashboard.php');
            exit;
        }
        $error = 'Sai tài khoản hoặc mật khẩu / tài khoản tạm khóa';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin auth">
    <div class="auth-card">
        <h2>Đăng nhập quản trị</h2>
        <?php if ($error): ?><p class="error"><?php echo e($error); ?></p><?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
            <label>Username</label>
            <input type="text" name="username" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <button class="btn" type="submit">Đăng nhập</button>
    </form>
</div>
</body>
</html>
