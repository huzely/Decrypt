<?php
require_once __DIR__ . '/../app/lib/helpers.php';
require_once __DIR__ . '/../app/lib/settings.php';
$settings = get_settings();
$siteName = $settings['site_name'] ?? 'News Portal';
$logo = $settings['logo_url'] ?? base_url('assets/img/logo.png');
$banner = $settings['banner_url'] ?? base_url('assets/img/banner.jpg');
$primary = $settings['primary_color'] ?? '#e63946';
$secondary = $settings['secondary_color'] ?? '#1d3557';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle ?? $siteName); ?></title>
    <?php if (!empty($metaDescription)): ?>
        <meta name="description" content="<?php echo e($metaDescription); ?>">
    <?php endif; ?>
    <?php if (!empty($ogImage)): ?>
        <meta property="og:image" content="<?php echo e($ogImage); ?>">
    <?php endif; ?>
    <meta property="og:title" content="<?php echo e($pageTitle ?? $siteName); ?>">
    <meta property="og:description" content="<?php echo e($metaDescription ?? 'Tin nhanh mỗi ngày'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/custom.css'); ?>">
    <style>
        :root { --primary: <?php echo e($primary); ?>; --secondary: <?php echo e($secondary); ?>; }
    </style>
    <script>window.__SITE__ = {adLink: <?php echo json_encode($settings['ad_link'] ?? ''); ?>};</script>
    <script defer src="<?php echo base_url('assets/js/app.js'); ?>"></script>
</head>
<body>
<header class="site-header">
    <div class="logo-wrap">
        <img src="<?php echo e($logo); ?>" alt="Logo" class="logo" loading="lazy">
        <div>
            <div class="site-name"><?php echo e($siteName); ?></div>
            <div class="tagline">Tin nóng cập nhật liên tục</div>
        </div>
    </div>
    <div class="banner">
        <img src="<?php echo e($banner); ?>" alt="Banner" loading="lazy">
    </div>
</header>
<main class="container">
<div id="ad-overlay" class="ad-overlay" hidden>
    <div class="ad-card">
        <button class="ad-close" type="button">×</button>
        <h3 id="ad-title"></h3>
        <p id="ad-body"></p>
        <p class="ad-note">Chạm bất kỳ đâu để tiếp tục</p>
    </div>
</div>
