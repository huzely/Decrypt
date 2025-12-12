<?php include __DIR__ . '/includes/header.php'; ?>
<?php
$settings = get_settings();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    save_settings($_POST);
    $settings = get_settings();
    redirect_with_message('/admin/settings.php', 'Đã lưu');
}
?>
<h1>Cài đặt</h1>
<form method="post">
    <div class="form-group"><label>Tên site</label><input name="site_name" value="<?= escape_html($settings['site_name']); ?>"></div>
    <div class="form-group"><label>Logo URL</label><input name="logo_url" value="<?= escape_html($settings['logo_url']); ?>"></div>
    <div class="form-group"><label>Banner URL</label><input name="banner_url" value="<?= escape_html($settings['banner_url']); ?>"></div>
    <div class="form-group"><label>Màu chủ đạo</label><input name="primary_color" value="<?= escape_html($settings['primary_color']); ?>"></div>
    <div class="form-group"><label>Footer text</label><input name="footer_text" value="<?= escape_html($settings['footer_text']); ?>"></div>
    <div class="form-group"><label>Ad title</label><input name="ad_title" value="<?= escape_html($settings['ad_title']); ?>"></div>
    <div class="form-group"><label>Ad body</label><textarea name="ad_body"><?= escape_html($settings['ad_body']); ?></textarea></div>
    <button class="btn primary" type="submit">Lưu</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
