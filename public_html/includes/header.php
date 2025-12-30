<?php
if (!isset($settings)) { $settings = []; }
$theme = isset($settings['theme']) ? (int)$settings['theme'] : 1;
?>
<!DOCTYPE html>
<html lang="vi" class="theme-<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <title><?php echo e($pageTitle ?? ($settings['site_name'] ?? 'Tin nhanh')); ?></title>
    <meta name="description" content="<?php echo e($pageDescription ?? ($settings['site_description'] ?? 'Tin tức cập nhật nhanh')); ?>">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/theme<?php echo $theme; ?>.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script defer src="/assets/js/app.js"></script>
    <script defer src="/assets/js/track.js"></script>
    <?php if (!empty($extraHead)) echo $extraHead; ?>
</head>
<body>
<header class="site-header container">
    <div class="logo">
        <?php if (!empty($settings['logo'])): ?>
            <img src="<?php echo e($settings['logo']); ?>" alt="logo" height="40">
        <?php endif; ?>
        <span><?php echo e($settings['site_name'] ?? 'Tin nhanh'); ?></span>
    </div>
    <div style="display:flex; align-items:center; gap:12px;">
        <?php include __DIR__ . '/menu.php'; ?>
    </div>
</header>
<?php if (!empty($settings['banner'])): ?>
<div class="container banner">
    <img src="<?php echo e($settings['banner']); ?>" alt="Banner" loading="lazy">
</div>
<?php endif; ?>
<main class="container" style="padding:20px 0;">
