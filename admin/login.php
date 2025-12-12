<?php
require_once __DIR__ . '/../functions.php';
if (is_logged_in()) {
    header('Location: /admin/dashboard.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = :u');
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_user'] = $user['username'];
        header('Location: /admin/dashboard.php');
        exit;
    } else {
        $error = 'Sai thông tin đăng nhập';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body style="display:flex;align-items:center;justify-content:center;min-height:100vh;">
<div class="post-detail" style="max-width:360px;width:90%;">
    <h2>Đăng nhập</h2>
    <?php if ($error): ?><div class="alert"><?= escape_html($error); ?></div><?php endif; ?>
    <form method="post">
        <div class="form-group"><label>Tài khoản</label><input name="username" required></div>
        <div class="form-group"><label>Mật khẩu</label><input type="password" name="password" required></div>
        <button class="btn primary" type="submit">Đăng nhập</button>
    </form>
</div>
</body>
</html>
