<?php
require_once __DIR__ . '/../bootstrap.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (login($pdo, $username, $password)) {
        header('Location: ' . $baseUrl . '/admin/dashboard.php');
        exit;
    }
    $message = 'Sai tài khoản hoặc mật khẩu';
}

$branding = get_branding($pdo);
?><!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập quản trị</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/static/css/style.css">
    <style><?php echo render_styles($branding); ?></style>
</head>
<body>
<header>
    <h1>Đăng nhập quản trị</h1>
    <p class="subtitle">Tài khoản mặc định: admin / 123456</p>
</header>
<div class="container">
    <div class="card" style="max-width:420px;margin:auto;">
        <?php if ($message): ?><div class="alert error"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
        <form method="post">
            <label>Tài khoản</label>
            <input type="text" name="username" required placeholder="admin">
            <label>Mật khẩu</label>
            <input type="password" name="password" required placeholder="123456">
            <button class="btn" type="submit">Đăng nhập</button>
        </form>
        <p class="subtitle" style="margin-top:12px;">Bạn có thể thêm tài khoản tại trang đăng ký admin.</p>
    </div>
</div>
</body>
</html>
