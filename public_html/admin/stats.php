<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/tracking.php';
$pdo = DB::conn();
$export = isset($_GET['export']);
$statsByPost = $pdo->query("SELECT slug, event_type, COUNT(*) as total FROM click_events GROUP BY slug, event_type")->fetchAll();
if ($export) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=\"stats.csv\"');
    echo "slug,event_type,total\n";
    foreach ($statsByPost as $row) {
        echo "{$row['slug']},{$row['event_type']},{$row['total']}\n";
    }
    exit;
}
$message = '';
if (isset($_POST['reset'])) {
    reset_tracking();
    $message = 'Đã reset';
    admin_log('reset_stats');
}
$stats = tracking_stats();
$views = $stats['article_view'] ?? 0;
$clicks = $stats['ad_forced_redirect'] ?? 0;
$ctr = $views ? round($clicks / $views * 100, 2) : 0;
include __DIR__ . '/includes/header.php';
?>
<section class="card admin-card">
    <h2>Thống kê</h2>
    <?php if ($message): ?><p class="success"><?php echo e($message); ?></p><?php endif; ?>
    <ul>
        <li>article_view: <?php echo $stats['article_view'] ?? 0; ?></li>
        <li>ad_forced_redirect: <?php echo $stats['ad_forced_redirect'] ?? 0; ?></li>
        <li>ad_close_click: <?php echo $stats['ad_close_click'] ?? 0; ?></li>
        <li>CTR: <?php echo $ctr; ?>%</li>
    </ul>
    <p><a class="btn secondary" href="?export=1">Export CSV</a></p>
    <form method="post">
        <button class="btn danger" name="reset" value="1">Reset</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
