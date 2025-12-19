<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/settings.php';
require_once __DIR__ . '/../app/lib/helpers.php';

$settings = get_settings();
$message = '';
$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
function handle_upload(string $field): ?string {
    global $uploadDir;
    if (empty($_FILES[$field]['name'])) return null;
    $tmp = $_FILES[$field]['tmp_name'];
    $type = mime_content_type($tmp);
    if (!in_array($type, ['image/png','image/jpeg','image/webp'])) return null;
    $name = uniqid($field . '_') . '.' . pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
    move_uploaded_file($tmp, $uploadDir . $name);
    return '/uploads/' . $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $message = 'Token không hợp lệ';
    } else {
        $logoUpload = handle_upload('logo_upload');
        $bannerUpload = handle_upload('banner_upload');
        $data = [
            'site_name' => sanitize_text($_POST['site_name'] ?? ''),
            'logo_url' => $logoUpload ?: sanitize_text($_POST['logo_url'] ?? ''),
            'banner_url' => $bannerUpload ?: sanitize_text($_POST['banner_url'] ?? ''),
            'hero_text' => sanitize_text($_POST['hero_text'] ?? ''),
            'meta_description' => sanitize_text($_POST['meta_description'] ?? ''),
            'theme' => sanitize_text($_POST['theme'] ?? 'theme-a'),
            'slug_mode' => sanitize_text($_POST['slug_mode'] ?? 'slug'),
            'ad_enabled' => isset($_POST['ad_enabled']) ? 1 : 0,
            'ad_link' => sanitize_text($_POST['ad_link'] ?? ''),
            'ad_title' => sanitize_text($_POST['ad_title'] ?? ''),
            'ad_body' => sanitize_text($_POST['ad_body'] ?? ''),
            'ad_frequency' => sanitize_text($_POST['ad_frequency'] ?? 'once'),
            'ad_interval_hours' => (int)($_POST['ad_interval_hours'] ?? 4),
            'ad_every_posts' => (int)($_POST['ad_every_posts'] ?? 3),
            'rate_window_seconds' => (int)($_POST['rate_window_seconds'] ?? 60),
            'rate_max_events' => (int)($_POST['rate_max_events'] ?? 5),
            'bot_list' => trim($_POST['bot_list'] ?? 'bot|crawl|spider|curl'),
            'telegram_token' => sanitize_text($_POST['telegram_token'] ?? ''),
            'telegram_chat_id' => sanitize_text($_POST['telegram_chat_id'] ?? ''),
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
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
        <label>Tên website</label>
        <input type="text" name="site_name" value="<?php echo e($settings['site_name'] ?? ''); ?>" required>
        <label>Logo URL (Telegram)</label>
        <input type="text" name="logo_url" value="<?php echo e($settings['logo_url'] ?? ''); ?>" required>
        <input type="file" name="logo_upload" accept="image/*">
        <label>Banner URL (Telegram)</label>
        <input type="text" name="banner_url" value="<?php echo e($settings['banner_url'] ?? ''); ?>" required>
        <input type="file" name="banner_upload" accept="image/*">
        <label>Hero text</label>
        <input type="text" name="hero_text" value="<?php echo e($settings['hero_text'] ?? ''); ?>">
        <label>Meta description</label>
        <input type="text" name="meta_description" value="<?php echo e($settings['meta_description'] ?? ''); ?>">
        <label>Theme</label>
        <select name="theme">
            <?php foreach (['theme-a'=>'Theme A','theme-b'=>'Theme B','theme-c'=>'Theme C'] as $k=>$v): ?>
                <option value="<?php echo e($k); ?>" <?php echo ($settings['theme'] ?? 'theme-a')===$k?'selected':''; ?>><?php echo e($v); ?></option>
            <?php endforeach; ?>
        </select>
        <label>Slug mode</label>
        <select name="slug_mode">
            <option value="slug" <?php echo ($settings['slug_mode'] ?? 'slug')==='slug'?'selected':''; ?>>/slug</option>
            <option value="slug-id" <?php echo ($settings['slug_mode'] ?? 'slug')==='slug-id'?'selected':''; ?>>/slug-id</option>
        </select>
        <hr>
        <h3>Quảng cáo Shopee</h3>
        <label><input type="checkbox" name="ad_enabled" value="1" <?php echo !empty($settings['ad_enabled'])?'checked':''; ?>> Bật interstitial</label>
        <label>Ad link (Shopee)</label>
        <input type="text" name="ad_link" value="<?php echo e($settings['ad_link'] ?? ''); ?>">
        <label>Ad title</label>
        <input type="text" name="ad_title" value="<?php echo e($settings['ad_title'] ?? ''); ?>">
        <label>Ad body</label>
        <textarea name="ad_body" rows="3"><?php echo e($settings['ad_body'] ?? ''); ?></textarea>
        <label>Tần suất hiển thị</label>
        <select name="ad_frequency">
            <option value="once" <?php echo ($settings['ad_frequency'] ?? 'once')==='once'?'selected':''; ?>>Một lần mỗi phiên</option>
            <option value="hourly" <?php echo ($settings['ad_frequency'] ?? '')==='hourly'?'selected':''; ?>>Mỗi N giờ</option>
            <option value="per_n_posts" <?php echo ($settings['ad_frequency'] ?? '')==='per_n_posts'?'selected':''; ?>>Mỗi N bài</option>
        </select>
        <label>Số giờ (nếu chọn mỗi N giờ)</label>
        <input type="number" name="ad_interval_hours" value="<?php echo e($settings['ad_interval_hours'] ?? 4); ?>">
        <label>Số bài (nếu chọn mỗi N bài)</label>
        <input type="number" name="ad_every_posts" value="<?php echo e($settings['ad_every_posts'] ?? 3); ?>">
        <hr>
        <h3>Chống spam/UA bot</h3>
        <label>Rate limit (số event tối đa)</label>
        <input type="number" name="rate_max_events" value="<?php echo e($settings['rate_max_events'] ?? 5); ?>">
        <label>Trong số giây</label>
        <input type="number" name="rate_window_seconds" value="<?php echo e($settings['rate_window_seconds'] ?? 60); ?>">
        <label>Danh sách UA bot (regex)</label>
        <textarea name="bot_list" rows="2"><?php echo e($settings['bot_list'] ?? 'bot|crawl|spider|curl'); ?></textarea>
        <hr>
        <h3>Telegram</h3>
        <label>Bot token</label>
        <input type="text" name="telegram_token" value="<?php echo e($settings['telegram_token'] ?? ''); ?>">
        <label>Chat ID</label>
        <input type="text" name="telegram_chat_id" value="<?php echo e($settings['telegram_chat_id'] ?? ''); ?>">
        <button class="btn" type="submit">Lưu</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
