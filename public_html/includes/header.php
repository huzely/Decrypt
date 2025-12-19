<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/lib/db.php';
require_once __DIR__ . '/../app/lib/cache.php';
require_once __DIR__ . '/../app/lib/auth.php';
require_once __DIR__ . '/../app/lib/csrf.php';
start_secure_session($config);
$pdo = db($config);
$settings = cache_get($config, 'settings');
if (!$settings) {
    $stmt = $pdo->query('SELECT * FROM site_settings WHERE id=1 LIMIT 1');
    $settings = $stmt->fetch() ?: [
        'site_name' => 'News Portal',
        'logo' => '',
        'banner' => '',
        'theme_color' => '#0d6efd',
        'ad_link' => '',
        'ad_title' => '',
        'ad_body' => '',
    ];
    cache_set($config, 'settings', $settings, $config['cache_ttl']['settings']);
}
?><!DOCTYPE html>
<html lang="vi" data-ad-link="<?= htmlspecialchars($settings['ad_link'] ?? '', ENT_QUOTES); ?>" data-ad-title="<?= htmlspecialchars($settings['ad_title'] ?? '', ENT_QUOTES); ?>" data-ad-body="<?= htmlspecialchars($settings['ad_body'] ?? '', ENT_QUOTES); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? $settings['site_name'], ENT_QUOTES); ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description ?? ($settings['ad_body'] ?? ''), ENT_QUOTES); ?>">
    <meta name="keywords" content="<?= htmlspecialchars($meta_keywords ?? '', ENT_QUOTES); ?>">
    <?php if (!empty($og_image)): ?>
    <meta property="og:image" content="<?= htmlspecialchars($og_image, ENT_QUOTES); ?>">
    <?php endif; ?>
    <?php if (!empty($og_url)): ?>
    <meta property="og:url" content="<?= htmlspecialchars($og_url, ENT_QUOTES); ?>">
    <?php endif; ?>
    <meta property="og:title" content="<?= htmlspecialchars($page_title ?? $settings['site_name'], ENT_QUOTES); ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta_description ?? '', ENT_QUOTES); ?>">
    <link rel="stylesheet" href="/assets/css/style.css?v=1">
    <link rel="stylesheet" href="/assets/css/custom.css?v=1">
    <style>:root { --primary: <?= htmlspecialchars($settings['theme_color'] ?? '#0d6efd', ENT_QUOTES); ?>; }</style>
</head>
<body>
    <header class="site-header">
        <div class="brand">
            <?php if (!empty($settings['logo'])): ?>
                <a href="/"><img src="<?= htmlspecialchars($settings['logo'], ENT_QUOTES); ?>" class="logo" alt="Logo"></a>
            <?php endif; ?>
            <div>
                <a href="/" class="site-name"><?= htmlspecialchars($settings['site_name'], ENT_QUOTES); ?></a>
                <?php if (!empty($settings['banner'])): ?>
                    <div class="banner"><img src="<?= htmlspecialchars($settings['banner'], ENT_QUOTES); ?>" alt="Banner"></div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="container">
