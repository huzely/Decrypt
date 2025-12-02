<?php
require_once __DIR__ . '/../bootstrap.php';
require_login();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    [$ok, $message] = create_admin($pdo, $username, $password);
    if ($ok) {
        $_SESSION['flash'] = $message;
        header('Location: /admin/dashboard.php');
        exit;
    }
}
?><!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm tài khoản admin</title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
<header>
    <h1>Tạo tài khoản quản trị</h1>
    <p class="subtitle"><a href="/admin/dashboard.php">← Quay lại bảng điều khiển</a></p>
</header>
<div class="container">
    <div class="card" style="max-width:520px;">
        <?php if ($message): ?><div class="alert error"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
        <form method="post">
            <label>Tài khoản</label>
            <input type="text" name="username" required>
            <label>Mật khẩu</label>
            <input type="password" name="password" required>
            <button class="btn" type="submit">Tạo tài khoản</button>
        </form>
    </div>
</div>
</body>
</html>
