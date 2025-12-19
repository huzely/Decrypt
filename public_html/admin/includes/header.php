<?php require __DIR__ . '/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="/assets/css/style.css?v=1">
</head>
<body>
<div class="admin-wrapper">
    <aside class="sidebar">
        <h3>Admin</h3>
        <a href="/admin/dashboard.php">Dashboard</a>
        <a href="/admin/posts.php">Bài viết</a>
        <a href="/admin/settings.php">Cài đặt</a>
        <a href="/admin/theme.php">Giao diện</a>
        <a href="/admin/stats.php">Thống kê</a>
        <a href="/admin/logout.php">Đăng xuất</a>
    </aside>
    <section class="admin-content">
