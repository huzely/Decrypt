<?php
require __DIR__ . '/includes/header.php';
$settings = $pdo->query('SELECT * FROM site_settings WHERE id=1 LIMIT 1')->fetch();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'site_name' => trim($_POST['site_name'] ?? ''),
        'logo' => trim($_POST['logo'] ?? ''),
        'banner' => trim($_POST['banner'] ?? ''),
        'theme_color' => trim($_POST['theme_color'] ?? '#0d6efd'),
        'footer_text' => trim($_POST['footer_text'] ?? ''),
        'ad_link' => trim($_POST['ad_link'] ?? ''),
        'ad_title' => trim($_POST['ad_title'] ?? ''),
        'ad_body' => trim($_POST['ad_body'] ?? ''),
    ];
    $stmt = $pdo->prepare('UPDATE site_settings SET site_name=?, logo=?, banner=?, theme_color=?, footer_text=?, ad_link=?, ad_title=?, ad_body=?, updated_at=NOW() WHERE id=1');
    $stmt->execute(array_values($data));
    cache_clear($config);
    $msg = 'Đã lưu';
    $settings = array_merge($settings, $data);
}
?>
<h1>Cài đặt</h1>
<?php if ($msg): ?><p style="color:green;"><?= htmlspecialchars($msg, ENT_QUOTES); ?></p><?php endif; ?>
<form method="post">
    <div class="form-group"><label>Tên site</label><input class="form-control" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '', ENT_QUOTES); ?>"></div>
    <div class="form-group"><label>Logo URL</label><input class="form-control" name="logo" value="<?= htmlspecialchars($settings['logo'] ?? '', ENT_QUOTES); ?>"></div>
    <div class="form-group"><label>Banner URL</label><input class="form-control" name="banner" value="<?= htmlspecialchars($settings['banner'] ?? '', ENT_QUOTES); ?>"></div>
    <div class="form-group"><label>Màu chủ đạo</label><input class="form-control" name="theme_color" value="<?= htmlspecialchars($settings['theme_color'] ?? '#0d6efd', ENT_QUOTES); ?>"></div>
    <div class="form-group"><label>Footer text</label><input class="form-control" name="footer_text" value="<?= htmlspecialchars($settings['footer_text'] ?? '', ENT_QUOTES); ?>"></div>
    <div class="form-group"><label>ad_link (Shopee)</label><input class="form-control" name="ad_link" value="<?= htmlspecialchars($settings['ad_link'] ?? '', ENT_QUOTES); ?>"></div>
    <div class="form-group"><label>ad_title</label><input class="form-control" name="ad_title" value="<?= htmlspecialchars($settings['ad_title'] ?? '', ENT_QUOTES); ?>"></div>
    <div class="form-group"><label>ad_body</label><textarea class="form-control" name="ad_body" rows="3"><?= htmlspecialchars($settings['ad_body'] ?? '', ENT_QUOTES); ?></textarea></div>
    <button class="btn btn-primary" type="submit">Lưu</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
