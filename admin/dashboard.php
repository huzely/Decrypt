<?php
require_once __DIR__ . '/includes/auth_check.php';
$stats = get_daily_stats($pdo);
include __DIR__ . '/includes/header.php';
?>
<h1>Dashboard</h1>
<ul>
    <li>Clicks Shopee hôm nay: <?= (int)$stats['clicks_today']; ?></li>
    <li>Bài đã publish hôm nay: <?= (int)$stats['published_today']; ?></li>
</ul>
<?php include __DIR__ . '/includes/footer.php'; ?>
