<?php
$settings = $settings ?? [];
$siteName = $settings['site_name'] ?? 'News Portal';
$logo = $settings['logo_url'] ?? asset('img/logo.png');
$banner = $settings['banner_url'] ?? asset('img/banner.jpg');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($siteName); ?></title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <script src="<?php echo asset('js/app.js'); ?>" defer></script>
</head>
<body>
<header class="site-header">
    <div class="logo-title">
        <img src="<?php echo e($logo); ?>" alt="Logo" class="logo">
        <div>
            <div class="site-name"><?php echo e($siteName); ?></div>
            <div class="site-tagline">Tin nóng cập nhật từng phút</div>
        </div>
    </div>
    <div class="banner">
        <img src="<?php echo e($banner); ?>" alt="Banner" loading="lazy">
    </div>
</header>
<main class="container">
