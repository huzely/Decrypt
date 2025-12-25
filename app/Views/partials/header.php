<?php use App\Core\Security; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Security::escape($meta['title'] ?? ($settings['site_name'] ?? 'Tin tức')) ?></title>
    <meta name="description" content="<?= Security::escape($meta['description'] ?? ($settings['site_description'] ?? '')) ?>">
    <meta property="og:title" content="<?= Security::escape($meta['og_title'] ?? ($settings['site_name'] ?? 'Tin tức')) ?>">
    <meta property="og:description" content="<?= Security::escape($meta['og_description'] ?? ($settings['site_description'] ?? '')) ?>">
    <?php if (!empty($meta['og_image'])): ?>
        <meta property="og:image" content="<?= Security::escape($meta['og_image']) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/base.css') ?>">
    <?php $theme = $settings['theme'] ?? 'theme-a'; ?>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/' . $theme . '.css') ?>">
</head>
<body class="<?= $theme ?>" data-base="<?= base_url('') ?>">
<script>window.csrfToken = "<?= csrf_token() ?>";</script>
<header class="site-header">
    <div class="branding">
        <?php if (!empty($settings['logo'])): ?>
            <img src="<?= base_url('public/' . $settings['logo']) ?>" alt="Logo" class="logo">
        <?php else: ?>
            <span class="brand-text"><?= Security::escape($settings['site_name'] ?? 'Tin tức nhanh') ?></span>
        <?php endif; ?>
    </div>
    <div class="header-actions">
        <form method="get" action="<?= base_url() ?>" class="search-form">
            <input type="text" name="q" placeholder="Tìm kiếm" value="<?= Security::escape($search ?? '') ?>">
            <button type="submit">🔍</button>
        </form>
        <div class="hamburger" id="hamburger">☰</div>
    </div>
</header>
<nav class="mobile-menu" id="mobileMenu">
    <ul>
        <li><a href="<?= base_url() ?>">Trang chủ</a></li>
        <?php foreach (($categories ?? []) as $cat): ?>
            <li><a href="<?= base_url('?category=' . $cat['id']) ?>"><?= Security::escape($cat['name']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</nav>
<main class="content">
