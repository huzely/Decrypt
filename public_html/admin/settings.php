<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/settings.php';
require_once __DIR__ . '/../app/lib/helpers.php';

$settings = get_settings();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $message = 'Token không hợp lệ';
    } else {
        $data = [
            'site_name' => sanitize_text($_POST['site_name'] ?? ''),
            'logo_url' => sanitize_text($_POST['logo_url'] ?? ''),
            'banner_url' => sanitize_text($_POST['banner_url'] ?? ''),
            'hero_text' => sanitize_text($_POST['hero_text'] ?? ''),
            'meta_description' => sanitize_text($_POST['meta_description'] ?? ''),
            'ad_link' => sanitize_text($_POST['ad_link'] ?? ''),
            'ad_title' => sanitize_text($_POST['ad_title'] ?? ''),
            'ad_body' => sanitize_text($_POST['ad_body'] ?? ''),
        ];
        save_settings($data + $settings);
        $settings = get_settings();
        $message = 'Đã lưu';
        admin_log('update_settings');
    }
}
include __DIR__ . '/includes/header.php';
?>
<section class="card admin-card">
    <h2>Cài đặt</h2>
    <?php if ($message): ?><p class="success"><?php echo e($message); ?></p><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
        <label>Tên website</label>
        <input type="text" name="site_name" value="<?php echo e($settings['site_name'] ?? ''); ?>" required>
        <label>Logo URL (Telegram)</label>
        <input type="text" name="logo_url" value="<?php echo e($settings['logo_url'] ?? ''); ?>" required>
        <label>Banner URL (Telegram)</label>
        <input type="text" name="banner_url" value="<?php echo e($settings['banner_url'] ?? ''); ?>" required>
        <label>Hero text</label>
        <input type="text" name="hero_text" value="<?php echo e($settings['hero_text'] ?? ''); ?>">
        <label>Meta description</label>
        <input type="text" name="meta_description" value="<?php echo e($settings['meta_description'] ?? ''); ?>">
        <hr>
        <h3>Quảng cáo Shopee</h3>
        <label>Ad link (Shopee)</label>
        <input type="text" name="ad_link" value="<?php echo e($settings['ad_link'] ?? ''); ?>">
        <label>Ad title</label>
        <input type="text" name="ad_title" value="<?php echo e($settings['ad_title'] ?? ''); ?>">
        <label>Ad body</label>
        <textarea name="ad_body" rows="3"><?php echo e($settings['ad_body'] ?? ''); ?></textarea>
        <button class="btn" type="submit">Lưu</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
