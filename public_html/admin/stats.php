<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_auth();

$message = '';
if (is_post()) {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $message = 'Token không hợp lệ';
    } else {
        $type = $_POST['reset_type'] ?? 'all';
        if ($type === 'ad_click') {
            $pdo->exec("DELETE FROM events WHERE event_type='ad_click'");
        } elseif ($type === 'post_view') {
            $pdo->exec("DELETE FROM events WHERE event_type='post_view'");
        } else {
            $pdo->exec('TRUNCATE TABLE events');
        }
        $message = 'Đã reset thống kê';
    }
}

$todayViews = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_type='post_view' AND DATE(created_at)=CURDATE()")->fetchColumn();
$monthViews = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_type='post_view' AND MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())")->fetchColumn();
$adClicks = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_type='ad_click'")->fetchColumn();
$daily = $pdo->query("SELECT DATE(created_at) as d, COUNT(*) as c FROM events WHERE event_type='post_view' GROUP BY DATE(created_at) ORDER BY d DESC LIMIT 15")->fetchAll();
$monthly = $pdo->query("SELECT DATE_FORMAT(created_at,'%Y-%m') as m, COUNT(*) as c FROM events WHERE event_type='post_view' GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY m DESC LIMIT 12")->fetchAll();
$topStmt = $pdo->query("SELECT slug, COUNT(*) as c FROM events WHERE event_type='post_view' GROUP BY slug ORDER BY c DESC LIMIT 5");
$topPosts = $topStmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div class="topbar"><h1>Thống kê</h1></div>
<?php if ($message): ?><div class="alert"><?php echo e($message); ?></div><?php endif; ?>
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
<div class="card" style="margin-top:16px;">
    <h3>Theo ngày</h3>
    <ul>
        <?php foreach ($daily as $d): ?>
            <li><?php echo e($d['d']); ?>: <?php echo $d['c']; ?> view</li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="card" style="margin-top:16px;">
    <h3>Theo tháng</h3>
    <ul>
        <?php foreach ($monthly as $m): ?>
            <li><?php echo e($m['m']); ?>: <?php echo $m['c']; ?> view</li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="card" style="margin-top:16px;">
    <h3>Reset thống kê</h3>
    <form method="POST">
        <select name="reset_type">
            <option value="all">Tất cả</option>
            <option value="post_view">Chỉ view</option>
            <option value="ad_click">Chỉ ad click</option>
        </select>
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <button class="btn" type="submit">Reset</button>
    </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
