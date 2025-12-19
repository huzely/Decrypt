<?php
require_once __DIR__ . '/../../app/lib/helpers.php';
require_once __DIR__ . '/../../app/lib/settings.php';
$settings = get_settings();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | <?php echo e($settings['site_name'] ?? 'News'); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="admin">
<header class="admin-header">
    <div class="admin-brand"><?php echo e($settings['site_name'] ?? 'News'); ?> Admin</div>
    <nav class="admin-nav">
        <a href="/admin/dashboard.php">Dashboard</a>
        <a href="/admin/posts.php">Bài viết</a>
        <a href="/admin/settings.php">Cài đặt</a>
        <a href="/admin/stats.php">Thống kê</a>
        <a href="/admin/theme.php">Giao diện</a>
        <a href="/admin/logout.php">Đăng xuất</a>
    </nav>
</header>
<main class="admin-container">
