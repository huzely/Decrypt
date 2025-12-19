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
            'primary_color' => sanitize_text($_POST['primary_color'] ?? '#e63946'),
            'secondary_color' => sanitize_text($_POST['secondary_color'] ?? '#1d3557'),
        ];
        save_settings($data + $settings);
        $settings = get_settings();
        $message = 'Đã lưu';
        admin_log('update_theme');
    }
}
include __DIR__ . '/includes/header.php';
?>
<section class="card admin-card">
    <h2>Giao diện</h2>
    <?php if ($message): ?><p class="success"><?php echo e($message); ?></p><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
        <label>Màu chính</label>
        <input type="color" name="primary_color" value="<?php echo e($settings['primary_color'] ?? '#e63946'); ?>">
        <label>Màu phụ</label>
        <input type="color" name="secondary_color" value="<?php echo e($settings['secondary_color'] ?? '#1d3557'); ?>">
        <button class="btn" type="submit">Lưu</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
