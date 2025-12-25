<?php use App\Core\Security; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/admin.css') ?>">
</head>
<body class="login-page">
    <form method="post" action="<?= base_url('admin/login') ?>" class="login-form">
        <h1>Đăng nhập</h1>
        <?php if (!empty($error)): ?><div class="alert"><?= Security::escape($error) ?></div><?php endif; ?>
        <?= csrf_field() ?>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>
