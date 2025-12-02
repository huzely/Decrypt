<?php
require_once __DIR__ . '/../helpers.php';

if (is_logged_in()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username']];
        header('Location: /admin/dashboard.php');
        exit;
    } else {
        $error = 'Sai tài khoản hoặc mật khẩu';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập quản trị</title>
    <link rel="stylesheet" href="/static/css/style.css">
    <style>
        body { background: #0f172a; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .panel { background:#111827; padding:2rem; border-radius:16px; width:360px; box-shadow:0 10px 30px rgba(0,0,0,.35); color:#e5e7eb; }
        input { width:100%; padding:0.7rem 0.9rem; border-radius:10px; border:1px solid #1f2937; margin-bottom:0.75rem; background:#0b1220; color:#e5e7eb; }
        button { width:100%; padding:0.75rem; border:none; border-radius:10px; background:#6366f1; color:#fff; font-weight:700; cursor:pointer; }
        a { color:#cbd5e1; text-decoration:none; }
        .error { background: rgba(248,113,113,.18); color:#fecdd3; padding:0.5rem 0.75rem; border-radius:10px; margin-bottom:0.75rem; }
    </style>
</head>
<body>
<div class="panel">
    <h2 style="margin-top:0;">Đăng nhập</h2>
    <?php if ($error): ?><div class="error"><?php echo sanitize($error); ?></div><?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng nhập</button>
    </form>
    <p style="margin-top:0.5rem;">Chưa có tài khoản? <a href="/admin/register.php">Đăng kí</a></p>
</div>
</body>
</html>
