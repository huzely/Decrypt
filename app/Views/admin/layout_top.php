<?php use App\Core\Security; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?= Security::escape($settings['site_name'] ?? 'Tin tức') ?></title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/admin.css') ?>">
</head>
<body>
<header class="admin-header">
    <div class="brand">Admin · <?= Security::escape($settings['site_name'] ?? 'Tin tức') ?></div>
    <nav>
        <a href="<?= base_url('admin') ?>">Dashboard</a>
        <a href="<?= base_url('admin/articles') ?>">Bài viết</a>
        <a href="<?= base_url('admin/settings') ?>">Cài đặt</a>
        <a href="<?= base_url('admin/logout') ?>">Đăng xuất</a>
    </nav>
</header>
<main class="admin-main">
<?php if (!empty($_SESSION['flash'])): ?>
    <div class="flash"><?= Security::escape($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
