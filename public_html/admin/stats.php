<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/db.php';
require_once __DIR__ . '/../app/lib/csrf.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_verify();
    if(isset($_POST['reset_all'])){
        db()->exec('TRUNCATE TABLE events');
    }
    header('Location: '.BASE_URL.'/admin/stats.php');
    exit;
}
$daily = db()->query('SELECT DATE(created_at) d, event_type, COUNT(*) c FROM events GROUP BY d, event_type ORDER BY d DESC LIMIT 30')->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<h3>Thống kê</h3>
<table>
<tr><th>Ngày</th><th>Loại</th><th>Số</th></tr>
<?php foreach($daily as $row): ?>
<tr><td><?= $row['d'] ?></td><td><?= $row['event_type'] ?></td><td><?= $row['c'] ?></td></tr>
<?php endforeach; ?>
</table>
<form method="post" onsubmit="return confirm('Reset toàn bộ?')">
    <?= csrf_field() ?>
    <button class="btn" name="reset_all" value="1">Reset thống kê</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
