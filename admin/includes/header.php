<?php
require_once __DIR__ . '/../../functions.php';
require_login();
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title>Admin</title>
</head>
<body>
<header class="site-header">
    <div class="container admin-header">
        <h1><a href="/admin/dashboard.php">Admin Dashboard</a></h1>
        <nav>
            <a href="/admin/posts.php">Bài viết</a> |
            <a href="/admin/logout.php">Đăng xuất</a>
        </nav>
    </div>
</header>
<main class="container">
<?php render_flash(); ?>
