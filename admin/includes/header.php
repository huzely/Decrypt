<?php require_once __DIR__ . '/../../functions.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title>Admin</title>
</head>
<body>
<header>
    <div class="container nav">
        <a href="/admin/dashboard.php">Dashboard</a>
        <a href="/admin/posts.php">Bài viết</a>
        <a href="/admin/stats.php">Thống kê</a>
        <a href="/admin/logout.php">Đăng xuất</a>
    </div>
</header>
<main class="container">
<?php render_flash(); ?>
