<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/lib/db.php';
require_once __DIR__ . '/../app/lib/auth.php';
require_once __DIR__ . '/../app/lib/csrf.php';

start_secure_session($config);
$pdo = db($config);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['token'] ?? '')) {
        $error = 'Mã bảo vệ không hợp lệ.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            header('Location: /admin/dashboard.php');
            exit;
        } else {
            $error = 'Sai tài khoản hoặc mật khẩu.';
        }
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
<body>
    <div class="container" style="max-width:420px;">
        <h1>Admin Login</h1>
        <?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES); ?></p><?php endif; ?>
        <form method="post">
            <input type="hidden" name="token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES); ?>">
            <div class="form-group"><label>Tài khoản</label><input class="form-control" name="username" required></div>
            <div class="form-group"><label>Mật khẩu</label><input class="form-control" type="password" name="password" required></div>
            <button class="btn btn-primary" type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>
