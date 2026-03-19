<?php
require_once __DIR__ . '/functions.php';
$settings = site_settings();
$announcement = get_announcement();
$headerAds = get_ads('header');
$middleAds = get_ads('middle');
$footerAds = get_ads('footer');
$popupAds = get_ads('popup');
$currentCategory = $_GET['category'] ?? '';
$currentTag = $_GET['tag'] ?? '';
$currentQuery = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc($settings['site_name']); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body style="--primary-color: <?php echo esc($settings['primary_color']); ?>;">
<header class="site-header">
    <div class="container nav-row">
        <a href="<?php echo BASE_URL; ?>public_html/index.php" class="brand">
            <?php if (!empty($settings['logo'])): ?>
                <img src="<?php echo esc($settings['logo']); ?>" alt="logo" class="brand-logo">
            <?php endif; ?>
            <span><?php echo esc($settings['site_name']); ?></span>
        </a>
        <form action="<?php echo BASE_URL; ?>public_html/search.php" method="get" class="search-form">
            <input type="text" name="q" value="<?php echo esc($currentQuery); ?>" placeholder="Search title or tags">
        </form>
        <nav class="top-links">
            <a href="<?php echo BASE_URL; ?>public_html/upload.php">Upload</a>
            <a href="<?php echo BASE_URL; ?>admin/login.php">Admin</a>
        </nav>
    </div>
</header>
<main class="container main-content">
    <?php if ($headerAds): $ad = $headerAds[0]; ?>
        <a class="banner-ad" href="<?php echo esc($ad['link']); ?>" target="_blank" rel="noreferrer"><img src="<?php echo esc($ad['image']); ?>" alt="Header ad"></a>
    <?php endif; ?>
    <?php if ($announcement): ?>
        <section class="panel announcement">
            <h2><?php echo esc($announcement['title']); ?></h2>
            <p><?php echo esc($announcement['content']); ?></p>
        </section>
    <?php endif; ?>
