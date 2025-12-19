<?php
require_once __DIR__ . '/../app/lib/helpers.php';
require_once __DIR__ . '/../app/lib/settings.php';
$settings = get_settings();
$siteName = $settings['site_name'] ?? 'News Portal';
$logo = $settings['logo_url'] ?? '';
$banner = $settings['banner_url'] ?? '';
$primary = $settings['primary_color'] ?? '#e63946';
$secondary = $settings['secondary_color'] ?? '#1d3557';
$theme = $settings['theme'] ?? 'theme-a';
$bodyClass = $theme;
$metaDescription = $metaDescription ?? ($settings['meta_description'] ?? 'Tin nhanh mỗi ngày');
$ogImage = $ogImage ?? ($settings['logo_url'] ?? '');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle ?? $siteName); ?></title>
    <meta name="description" content="<?php echo e($metaDescription); ?>">
    <meta property="og:title" content="<?php echo e($pageTitle ?? $siteName); ?>">
    <meta property="og:description" content="<?php echo e($metaDescription); ?>">
    <?php if ($ogImage): ?><meta property="og:image" content="<?php echo e($ogImage); ?>"><?php endif; ?>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/custom.css'); ?>">
    <style>
        :root { --primary: <?php echo e($primary); ?>; --secondary: <?php echo e($secondary); ?>; }
    </style>
    <script>window.__SITE__ = {adLink: <?php echo json_encode($settings['ad_link'] ?? ''); ?>};</script>
    <script defer src="<?php echo base_url('assets/js/app.js'); ?>"></script>
</head>
<body class="<?php echo e($bodyClass); ?>">
<header class="site-header">
    <div class="logo-wrap">
        <?php if ($logo): ?><img src="<?php echo e($logo); ?>" alt="Logo" class="logo" loading="lazy"><?php endif; ?>
        <div>
            <div class="site-name"><?php echo e($siteName); ?></div>
            <div class="tagline"><?php echo e($settings['hero_text'] ?? 'Tin nóng cập nhật liên tục'); ?></div>
        </div>
    </div>
    <div class="actions">
        <button class="hamburger" id="nav-toggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
        <?php if ($banner): ?>
        <div class="banner">
            <img src="<?php echo e($banner); ?>" alt="Banner" loading="lazy">
        </div>
        <?php endif; ?>
    </div>
    <nav class="nav-drawer" id="nav-drawer">
        <a href="/">Trang chủ</a>
        <a href="/?category=tin-nong">Tin nóng</a>
        <a href="/?category=phan-tich">Phân tích</a>
        <a href="/?category=doi-song">Đời sống</a>
        <form class="search-form" method="get" action="/">
            <input type="text" name="q" placeholder="Tìm kiếm...">
        </form>
    </nav>
</header>
<main class="container">
<div id="ad-overlay" class="ad-overlay" hidden>
    <div class="ad-card">
        <button class="ad-close" type="button">×</button>
        <h3 id="ad-title"></h3>
        <p id="ad-body"></p>
        <div class="ad-actions">
            <button class="btn shopee-go" id="ad-go">Đi tới Shopee (Ưu đãi)</button>
            <button class="btn secondary" id="ad-skip">Vào đọc bài (Bỏ qua)</button>
        </div>
    </div>
</div>
