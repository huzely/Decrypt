<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/cache.php';
require_once __DIR__ . '/../lib/db.php';

$settings = cache_get('site_settings', 300);
if (!$settings) {
    $settings = db()->query('SELECT * FROM site_settings LIMIT 1')->fetch();
    cache_set('site_settings', $settings);
}
$theme = $settings['theme'] ?? '1';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($settings['site_name'] ?? 'Tin tức nhanh') ?></title>
    <meta name="description" content="<?= htmlspecialchars($settings['site_description'] ?? '') ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/theme<?= $theme ?>.css">
    <script defer src="<?= BASE_URL ?>/assets/js/app.js"></script>
    <script defer src="<?= BASE_URL ?>/assets/js/adflow.js"></script>
    <script defer src="<?= BASE_URL ?>/assets/js/track.js"></script>
</head>
<body data-base="<?= BASE_URL ?>">
<header class="site-header">
    <div class="brand">
        <?php if (!empty($settings['logo_path'])): ?>
            <img src="<?= BASE_URL . '/' . $settings['logo_path'] ?>" alt="Logo" class="logo">
        <?php else: ?>
            <span><?= htmlspecialchars($settings['site_name'] ?? 'Tin tức') ?></span>
        <?php endif; ?>
    </div>
    <div class="banner">
        <?php if (!empty($settings['banner_path'])): ?>
            <img src="<?= BASE_URL . '/' . $settings['banner_path'] ?>" alt="Banner">
        <?php endif; ?>
    </div>
    <div class="hamburger" id="hamburger">☰</div>
</header>
<?php include __DIR__ . '/menu.php'; ?>
<main class="container">
