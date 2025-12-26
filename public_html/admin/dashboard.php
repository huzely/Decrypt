<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/db.php';
include __DIR__ . '/includes/header.php';
$stats = db()->query('SELECT event_type, COUNT(*) as c FROM events GROUP BY event_type')->fetchAll();
$byType = []; foreach ($stats as $s){$byType[$s['event_type']] = $s['c'];}
?>
<div class="card">
    <h3>Tổng quan</h3>
    <p>Page views: <?= $byType['page_view'] ?? 0 ?></p>
    <p>Post views: <?= $byType['post_view'] ?? 0 ?></p>
    <p>Ad clicks: <?= $byType['ad_click'] ?? 0 ?></p>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
