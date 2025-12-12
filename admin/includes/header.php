<?php
require_once __DIR__ . '/../../functions.php';
require_login();
$settings = get_settings();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?= escape_html($settings['site_name'] ?? 'Tin tức'); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="admin-layout">
    <div class="admin-sidebar">
        <h2><?= escape_html($settings['site_name'] ?? 'Admin'); ?></h2>
        <nav class="admin-nav">
            <a href="/admin/dashboard.php">Dashboard</a>
            <a href="/admin/posts.php">Bài viết</a>
            <a href="/admin/stats.php">Thống kê</a>
            <a href="/admin/settings.php">Cài đặt</a>
            <a href="/admin/theme.php">Giao diện</a>
            <a href="/admin/logout.php">Đăng xuất</a>
        </nav>
    </div>
    <div class="admin-content">
        <div class="hamburger"><span></span><span></span><span></span></div>
