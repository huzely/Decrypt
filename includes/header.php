<?php
require_once __DIR__ . '/../functions.php';
$settings = get_settings();
$primary = $settings['primary_color'] ?? '#ff5722';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape_html($settings['site_name'] ?? 'Cổng tin tức Telegram'); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
    <style>:root{--primary-color: <?= escape_html($primary); ?>;}</style>
</head>
<body>
<div data-ad-title="<?= escape_html($settings['ad_title'] ?? 'Quảng cáo'); ?>" data-ad-body="<?= escape_html($settings['ad_body'] ?? 'Ưu đãi hấp dẫn trên Shopee'); ?>"></div>
<header class="site-header">
    <?php if (!empty($settings['logo_url'])): ?>
        <div class="logo"><img src="<?= escape_html($settings['logo_url']); ?>" alt="logo"></div>
    <?php endif; ?>
    <div class="title-area">
        <h1><?= escape_html($settings['site_name'] ?? 'Cổng tin tức Telegram'); ?></h1>
        <?php if (!empty($settings['banner_url'])): ?>
            <div class="banner"><img src="<?= escape_html($settings['banner_url']); ?>" alt="banner"></div>
        <?php endif; ?>
    </div>
</header>
<main class="container">
