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
    $confirm = $_POST['confirm'] ?? '';

    if (strlen($username) < 3 || strlen($password) < 6) {
        $error = 'Tên đăng nhập hoặc mật khẩu quá ngắn';
    } elseif ($password !== $confirm) {
        $error = 'Mật khẩu nhập lại không khớp';
    } else {
        $pdo = get_pdo();
        $exists = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $exists->execute([$username]);
        if ($exists->fetch()) {
            $error = 'Tên đăng nhập đã tồn tại';
        } else {
            $stmt = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
            $stmt->execute([$username, password_hash($password, PASSWORD_BCRYPT)]);
            $_SESSION['user'] = ['id' => $pdo->lastInsertId(), 'username' => $username];
            header('Location: /admin/dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng kí quản trị</title>
    <link rel="stylesheet" href="/static/css/style.css">
    <style>
        body { background: #0f172a; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .panel { background:#111827; padding:2rem; border-radius:16px; width:380px; box-shadow:0 10px 30px rgba(0,0,0,.35); color:#e5e7eb; }
        input { width:100%; padding:0.7rem 0.9rem; border-radius:10px; border:1px solid #1f2937; margin-bottom:0.75rem; background:#0b1220; color:#e5e7eb; }
        button { width:100%; padding:0.75rem; border:none; border-radius:10px; background:#22c55e; color:#0b1220; font-weight:700; cursor:pointer; }
        a { color:#cbd5e1; text-decoration:none; }
        .error { background: rgba(248,113,113,.18); color:#fecdd3; padding:0.5rem 0.75rem; border-radius:10px; margin-bottom:0.75rem; }
    </style>
</head>
<body>
<div class="panel">
    <h2 style="margin-top:0;">Tạo tài khoản</h2>
    <?php if ($error): ?><div class="error"><?php echo sanitize($error); ?></div><?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <input type="password" name="confirm" placeholder="Nhập lại mật khẩu" required>
        <button type="submit">Đăng kí</button>
    </form>
    <p style="margin-top:0.5rem;">Đã có tài khoản? <a href="/admin/login.php">Đăng nhập</a></p>
</div>
</body>
</html>
