<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'CSRF token invalid.';
    } else {
        $stmt = Database::connection()->prepare('SELECT * FROM admins WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => trim($_POST['email'])]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($_POST['password'], $admin['password'])) {
            $_SESSION['admin'] = $admin;
            header('Location: /genzmovie/admin/index.php');
            exit;
        }
        $error = 'Sai thông tin đăng nhập';
    }
}
?>
<!doctype html><html><head><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"></head>
<body class="bg-dark text-light d-flex align-items-center" style="height:100vh"><div class="container"><div class="col-md-4 mx-auto">
<h3>Admin Login</h3><?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="POST"><?= csrf_field() ?><input class="form-control mb-2" name="email" type="email" required><input class="form-control mb-2" name="password" type="password" required><button class="btn btn-danger w-100">Login</button></form>
</div></div></body></html>
