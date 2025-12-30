<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/csrf.php';

$message = '';
if (is_post()) {
    $token = $_POST['csrf'] ?? '';
    if (!verify_csrf($token)) {
        $message = 'Token không hợp lệ';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (login($username, $password, $pdo)) {
            redirect('/admin/dashboard.php');
        }
        $message = 'Sai tài khoản hoặc mật khẩu';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <title>Đăng nhập quản trị</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body style="display:grid; place-items:center; min-height:100vh;">
    <div class="card" style="width:min(420px, 92%);">
        <h2>Đăng nhập</h2>
        <?php if ($message): ?><div class="alert"><?php echo e($message); ?></div><?php endif; ?>
        <form method="POST">
            <label>Tài khoản</label>
            <input type="text" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" required>
            <label>Mật khẩu</label>
            <input type="password" name="password" required>
            <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
            <button class="btn" type="submit">Đăng nhập</button>
        </form>
        <p style="margin-top:12px; color:#94a3b8;">Tài khoản mặc định: admin / admin567</p>
    </div>
</body>
</html>
