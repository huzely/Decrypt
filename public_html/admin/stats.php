<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/db.php';
require_once __DIR__ . '/../app/lib/csrf.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_verify();
    if(isset($_POST['reset_all'])){
        db()->exec('TRUNCATE TABLE events');
    }
    if(isset($_POST['reset_selected']) && in_array($_POST['reset_type'], ['page_view','post_view','ad_click'])){
        $del = db()->prepare('DELETE FROM events WHERE event_type = :t');
        $del->execute([':t'=>$_POST['reset_type']]);
    }
    header('Location: '.BASE_URL.'/admin/stats.php');
    exit;
}
$daily = db()->query("SELECT DATE(created_at) d, event_type, COUNT(*) c FROM events GROUP BY d, event_type ORDER BY d DESC LIMIT 30")->fetchAll();
$topPosts = db()->query("SELECT slug, SUM(event_type='post_view') pv, SUM(event_type='ad_click') ac FROM events WHERE slug IS NOT NULL GROUP BY slug ORDER BY pv DESC LIMIT 10")->fetchAll();
$totals = db()->query("SELECT SUM(event_type='page_view') pv_all, SUM(event_type='post_view') post_all, SUM(event_type='ad_click') ad_all FROM events")->fetch();
$counts = db()->query("SELECT COUNT(*) total, SUM(status='public') public_count FROM articles")->fetch();
include __DIR__ . '/includes/header.php';
?>
<h3>Thống kê</h3>
<table>
<tr><th>Ngày</th><th>Loại</th><th>Số</th></tr>
<?php foreach($daily as $row): ?>
<tr><td><?= $row['d'] ?></td><td><?= $row['event_type'] ?></td><td><?= $row['c'] ?></td></tr>
<?php endforeach; ?>
</table>
<div class="card">
    <h4>Tổng</h4>
    <p>Page view: <?= $totals['pv_all'] ?? 0 ?> | Post view: <?= $totals['post_all'] ?? 0 ?> | Ad click: <?= $totals['ad_all'] ?? 0 ?></p>
    <p>Số bài: <?= $counts['total'] ?? 0 ?> (public: <?= $counts['public_count'] ?? 0 ?>)</p>
</div>
<h4>Top bài</h4>
<table>
    <tr><th>Slug</th><th>View</th><th>Ad click</th></tr>
    <?php foreach($topPosts as $p): ?>
    <tr><td><?= htmlspecialchars($p['slug']) ?></td><td><?= $p['pv'] ?></td><td><?= $p['ac'] ?></td></tr>
    <?php endforeach; ?>
</table>
<form method="post" onsubmit="return confirm('Reset toàn bộ?')">
    <?= csrf_field() ?>
    <button class="btn" name="reset_all" value="1">Reset toàn bộ</button>
</form>
<form method="post" onsubmit="return confirm('Reset theo loại?')">
    <?= csrf_field() ?>
    <select name="reset_type">
        <option value="page_view">Reset page_view</option>
        <option value="post_view">Reset post_view</option>
        <option value="ad_click">Reset ad_click</option>
    </select>
    <button class="btn" name="reset_selected" value="1">Reset loại</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
