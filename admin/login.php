<?php
require_once __DIR__ . '/../functions.php';

if (is_logged_in()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = :u LIMIT 1');
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
    <link rel="stylesheet" href="/assets/css/style.css">
    <title>Admin Login</title>
</head>
<body>
<div class="container">
    <h1>Đăng nhập Admin</h1>
    <?php if ($error): ?><div class="alert"><?= escape_html($error); ?></div><?php endif; ?>
    <form method="post">
        <div>
            <label>Tên đăng nhập</label><br>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Mật khẩu</label><br>
            <input type="password" name="password" required>
        </div>
        <button class="button" type="submit">Đăng nhập</button>
    </form>
</div>
</body>
</html>
