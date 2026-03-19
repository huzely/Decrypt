<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$stats = dashboard_stats();
$videos = fetch_videos();
$categories = get_categories();
$tags = get_tags();
$ads = get_ads(null, false);
$announcement = get_announcement();
$settings = site_settings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar panel">
        <h2>Admin Panel</h2>
        <p class="muted">Logged in as <?php echo esc($_SESSION['admin_username']); ?></p>
        <nav class="admin-nav">
            <a href="index.php">Dashboard</a>
            <a href="videos.php">Videos</a>
            <a href="taxonomy.php">Categories & Tags</a>
            <a href="ads.php">Ads</a>
            <a href="announcement.php">Announcement</a>
            <a href="settings.php">Settings</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
        <section class="stats-grid">
            <div class="panel stat-card"><span>Total Videos</span><strong><?php echo $stats['total_videos']; ?></strong></div>
            <div class="panel stat-card"><span>Total Views</span><strong><?php echo $stats['total_views']; ?></strong></div>
        </section>
        <section class="panel">
            <h2>Daily Views</h2>
            <table class="data-table"><tr><th>Date</th><th>Views</th></tr><?php foreach ($stats['daily'] as $row): ?><tr><td><?php echo esc($row['label']); ?></td><td><?php echo (int) $row['count']; ?></td></tr><?php endforeach; ?></table>
        </section>
        <section class="panel">
            <h2>Monthly Views</h2>
            <table class="data-table"><tr><th>Month</th><th>Views</th></tr><?php foreach ($stats['monthly'] as $row): ?><tr><td><?php echo esc($row['label']); ?></td><td><?php echo (int) $row['count']; ?></td></tr><?php endforeach; ?></table>
        </section>
        <section class="panel">
            <h2>Quick Overview</h2>
            <p class="muted">Videos: <?php echo count($videos); ?> · Categories: <?php echo count($categories); ?> · Tags: <?php echo count($tags); ?> · Ads: <?php echo count($ads); ?> · Announcement: <?php echo $announcement ? 'Active' : 'None'; ?> · Primary color: <?php echo esc($settings['primary_color']); ?></p>
        </section>
    </main>
</div>
</body>
</html>
