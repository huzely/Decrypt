<?php
require_once __DIR__ . '/includes/auth_check.php';
$stats = get_daily_stats($pdo);
$topPosts = get_top_posts($pdo);
include __DIR__ . '/includes/header.php';
?>
<h1>Thống kê</h1>
<p>Clicks hôm nay: <?= (int)$stats['clicks_today']; ?></p>
<p>Bài publish hôm nay: <?= (int)$stats['published_today']; ?></p>
<h2>Top bài theo click</h2>
<table class="admin-table">
    <tr><th>ID</th><th>Tiêu đề</th><th>Clicks</th></tr>
    <?php foreach ($topPosts as $row): $safe = escape_output($row, ['title']); ?>
        <tr><td><?= (int)$row['id']; ?></td><td><?= $safe['title']; ?></td><td><?= (int)$row['shopee_click_count']; ?></td></tr>
    <?php endforeach; ?>
</table>
<?php include __DIR__ . '/includes/footer.php'; ?>
