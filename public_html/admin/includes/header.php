<?php require_once __DIR__ . '/../../app/config/config.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Admin</title><link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css"></head>
<body>
<header class="admin-header">
    <div>Admin Panel</div>
    <nav>
        <a href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a>
        <a href="<?= BASE_URL ?>/admin/posts.php">Bài viết</a>
        <a href="<?= BASE_URL ?>/admin/settings.php">Cài đặt</a>
        <a href="<?= BASE_URL ?>/admin/stats.php">Thống kê</a>
        <a href="<?= BASE_URL ?>/admin/logout.php">Logout</a>
    </nav>
</header>
<main class="admin-main">
