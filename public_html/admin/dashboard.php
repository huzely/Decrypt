<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/posts.php';
require_once __DIR__ . '/../app/lib/tracking.php';
$stats = tracking_stats();
$totalPosts = count_posts();
$views = $stats['article_view'] ?? 0;
$clicks = $stats['ad_forced_redirect'] ?? 0;
$ctr = $views ? round($clicks / $views * 100, 2) : 0;
include __DIR__ . '/includes/header.php';
?>
<section class="card admin-card">
    <h2>Tổng quan</h2>
    <div class="stats-grid">
        <div class="stat"><div class="stat-number"><?php echo $totalPosts; ?></div><div class="stat-label">Bài public</div></div>
        <div class="stat"><div class="stat-number"><?php echo $stats['article_view'] ?? 0; ?></div><div class="stat-label">Lượt đọc</div></div>
        <div class="stat"><div class="stat-number"><?php echo $stats['ad_forced_redirect'] ?? 0; ?></div><div class="stat-label">Redirect quảng cáo</div></div>
        <div class="stat"><div class="stat-number"><?php echo $ctr; ?>%</div><div class="stat-label">CTR</div></div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
