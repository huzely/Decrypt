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

    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_user'] = $user['username'];
        redirect_with_message('/admin/dashboard.php', 'Đăng nhập thành công');
    } else {
        $error = 'Sai thông tin đăng nhập';
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title>Đăng nhập Admin</title>
</head>
<body>
<div class="container" style="max-width:400px; margin-top:40px;">
    <h2>Đăng nhập</h2>
    <?php if ($error): ?>
        <div class="alert"><?php echo escape_html($error); ?></div>
    <?php endif; ?>
    <form method="post">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Đăng nhập</button>
    </form>
</div>
</body>
</html>
