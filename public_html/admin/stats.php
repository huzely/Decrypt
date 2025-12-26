<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_auth();

$todayViews = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_type='post_view' AND DATE(created_at)=CURDATE()")->fetchColumn();
$monthViews = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_type='post_view' AND MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())")->fetchColumn();
$adClicks = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_type='ad_click'")->fetchColumn();
$topStmt = $pdo->query("SELECT slug, COUNT(*) as c FROM events WHERE event_type='post_view' GROUP BY slug ORDER BY c DESC LIMIT 5");
$topPosts = $topStmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div class="topbar"><h1>Thống kê</h1></div>
<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px;">
    <div class="card">View hôm nay: <strong><?php echo $todayViews; ?></strong></div>
    <div class="card">View tháng: <strong><?php echo $monthViews; ?></strong></div>
    <div class="card">Ad click: <strong><?php echo $adClicks; ?></strong></div>
</div>
<div class="card" style="margin-top:16px;">
    <h3>Top bài</h3>
    <ul>
        <?php foreach ($topPosts as $t): ?>
            <li><?php echo e($t['slug']); ?> - <?php echo $t['c']; ?> lượt</li>
        <?php endforeach; ?>
    </ul>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
