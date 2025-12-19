<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/tracking.php';
$message = '';
if (isset($_POST['reset'])) {
    reset_tracking();
    $message = 'Đã reset';
    admin_log('reset_stats');
}
$stats = tracking_stats();
include __DIR__ . '/includes/header.php';
?>
<section class="card admin-card">
    <h2>Thống kê</h2>
    <?php if ($message): ?><p class="success"><?php echo e($message); ?></p><?php endif; ?>
    <ul>
        <li>article_view: <?php echo $stats['article_view'] ?? 0; ?></li>
        <li>ad_forced_redirect: <?php echo $stats['ad_forced_redirect'] ?? 0; ?></li>
        <li>ad_close_click: <?php echo $stats['ad_close_click'] ?? 0; ?></li>
    </ul>
    <form method="post">
        <button class="btn danger" name="reset" value="1">Reset</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
