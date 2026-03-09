<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/partials.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logo = trim($_POST['site_logo'] ?? '');
    $banner = trim($_POST['site_banner'] ?? '');

    foreach (['site_logo' => $logo, 'site_banner' => $banner] as $key => $value) {
        $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        $stmt->execute(['k' => $key, 'v' => $value]);
    }
    header('Location: settings.php?saved=1');
    exit;
}
adminHeader('Settings');
?>
<h2>Settings</h2>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Đã lưu cài đặt</div><?php endif; ?>
<div class="card card-dark p-3">
<form method="post" class="row g-3">
<div class="col-md-12"><label>Logo URL</label><input class="form-control" name="site_logo" value="<?= e(setting($pdo, 'site_logo')) ?>"></div>
<div class="col-md-12"><label>Banner URL</label><input class="form-control" name="site_banner" value="<?= e(setting($pdo, 'site_banner')) ?>"></div>
<div class="col-md-12"><button class="btn btn-danger">Lưu</button></div>
</form></div>
<?php adminFooter(); ?>
