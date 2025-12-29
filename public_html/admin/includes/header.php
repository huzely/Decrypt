<?php
require_once __DIR__ . '/../includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <title>Quản trị</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <h2>Admin</h2>
        <a href="/admin/dashboard.php">Bảng tin</a>
        <a href="/admin/posts.php">Bài viết</a>
        <a href="/admin/post_add.php">Thêm bài</a>
        <a href="/admin/settings.php">Cài đặt</a>
        <a href="/admin/stats.php">Thống kê</a>
        <a href="/admin/logout.php">Đăng xuất</a>
    </aside>
    <main class="main">
        <button class="mobile-toggle" id="openNav">☰ Menu</button>
