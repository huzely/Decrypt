<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/telegram.php';
require_auth();
$pageTitle = 'Bảng tin';

flushTelegramQueue();
$totalArticles = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$publicArticles = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status='public'")->fetchColumn();
$views = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_type='post_view'")->fetchColumn();
$adClicks = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_type='ad_click'")->fetchColumn();
include __DIR__ . '/includes/header.php';
?>
<div class="topbar">
    <h1>Chào, <?php echo e($_SESSION['admin_username'] ?? 'admin'); ?></h1>
    <a class="btn" href="/">Xem trang</a>
</div>
<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px;">
    <div class="card">Tổng bài viết: <strong><?php echo $totalArticles; ?></strong></div>
    <div class="card">Bài public: <strong><?php echo $publicArticles; ?></strong></div>
    <div class="card">Lượt xem bài: <strong><?php echo $views; ?></strong></div>
    <div class="card">Click quảng cáo: <strong><?php echo $adClicks; ?></strong></div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
