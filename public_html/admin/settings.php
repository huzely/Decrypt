<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_auth();

$stmt = $pdo->query('SELECT * FROM site_settings LIMIT 1');
$settings = $stmt->fetch() ?: [
    'site_name' => 'Tin nhanh',
    'site_description' => '',
    'theme' => 1,
    'logo' => '',
    'banner' => '',
    'ads_enabled' => 0,
    'ad_link' => '',
    'ad_title' => '',
    'ad_body' => ''
];

$message = '';
if (is_post()) {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $message = 'Token không hợp lệ';
    } else {
        $site_name = trim($_POST['site_name'] ?? '');
        $site_description = trim($_POST['site_description'] ?? '');
        $theme = (int)($_POST['theme'] ?? 1);
        $ads_enabled = isset($_POST['ads_enabled']) ? 1 : 0;
        $ad_link = trim($_POST['ad_link'] ?? '');
        $ad_title = trim($_POST['ad_title'] ?? '');
        $ad_body = trim($_POST['ad_body'] ?? '');
        $logoPath = $settings['logo'] ?? '';
        $bannerPath = $settings['banner'] ?? '';

        if (!empty($_FILES['logo']['name'])) {
            $logoPath = '/assets/img/logo_' . time() . '_' . basename($_FILES['logo']['name']);
            move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__ . '/../' . ltrim($logoPath, '/'));
        }
        if (!empty($_FILES['banner']['name'])) {
            $bannerPath = '/assets/img/banner_' . time() . '_' . basename($_FILES['banner']['name']);
            move_uploaded_file($_FILES['banner']['tmp_name'], __DIR__ . '/../' . ltrim($bannerPath, '/'));
        }

        if (isset($settings['id'])) {
            $stmtU = $pdo->prepare('UPDATE site_settings SET site_name=:sn, site_description=:sd, theme=:t, logo=:logo, banner=:banner, ads_enabled=:ae, ad_link=:al, ad_title=:ati, ad_body=:abo WHERE id=:id');
            $stmtU->execute([
                ':sn' => $site_name,
                ':sd' => $site_description,
                ':t' => $theme,
                ':logo' => $logoPath,
                ':banner' => $bannerPath,
                ':ae' => $ads_enabled,
                ':al' => $ad_link,
                ':ati' => $ad_title,
                ':abo' => $ad_body,
                ':id' => $settings['id'],
            ]);
        } else {
            $stmtI = $pdo->prepare('INSERT INTO site_settings(site_name, site_description, theme, logo, banner, ads_enabled, ad_link, ad_title, ad_body) VALUES(:sn,:sd,:t,:logo,:banner,:ae,:al,:ati,:abo)');
            $stmtI->execute([
                ':sn' => $site_name,
                ':sd' => $site_description,
                ':t' => $theme,
                ':logo' => $logoPath,
                ':banner' => $bannerPath,
                ':ae' => $ads_enabled,
                ':al' => $ad_link,
                ':ati' => $ad_title,
                ':abo' => $ad_body,
            ]);
        }
        cache_set('site_settings', null, 1);
        $message = 'Đã lưu cài đặt';
        $settings = array_merge($settings, compact('site_name','site_description','theme','logoPath','bannerPath','ads_enabled','ad_link','ad_title','ad_body'));
        $settings['logo'] = $logoPath;
        $settings['banner'] = $bannerPath;
    }
}
include __DIR__ . '/includes/header.php';
?>
<div class="topbar"><h1>Cài đặt</h1></div>
<?php if ($message): ?><div class="alert"><?php echo e($message); ?></div><?php endif; ?>
<form class="form-section" method="POST" enctype="multipart/form-data">
    <label>Tên trang</label>
    <input name="site_name" value="<?php echo e($settings['site_name']); ?>" required>
    <label>Mô tả</label>
    <textarea name="site_description" rows="2"><?php echo e($settings['site_description']); ?></textarea>
    <label>Theme</label>
    <select name="theme">
        <option value="1" <?php echo ($settings['theme']==1?'selected':''); ?>>Theme 1</option>
        <option value="2" <?php echo ($settings['theme']==2?'selected':''); ?>>Theme 2</option>
        <option value="3" <?php echo ($settings['theme']==3?'selected':''); ?>>Theme 3</option>
    </select>
    <label>Logo (ảnh nhỏ)</label>
    <input type="file" name="logo" accept="image/*">
    <?php if (!empty($settings['logo'])): ?><img src="<?php echo e($settings['logo']); ?>" alt="logo" style="max-height:60px; margin:8px 0;"><?php endif; ?>
    <label>Banner</label>
    <input type="file" name="banner" accept="image/*">
    <?php if (!empty($settings['banner'])): ?><img src="<?php echo e($settings['banner']); ?>" alt="banner" style="max-width:100%; margin:8px 0;"><?php endif; ?>

    <label style="display:flex; align-items:center; gap:8px;"><input type="checkbox" name="ads_enabled" value="1" <?php echo ($settings['ads_enabled']??0)?'checked':''; ?>> Bật quảng cáo Shopee</label>
    <label>Link quảng cáo Shopee (https://s.shopee.vn/xxxx)</label>
    <input type="url" name="ad_link" value="<?php echo e($settings['ad_link']); ?>" placeholder="https://s.shopee.vn/...">
    <label>Tiêu đề quảng cáo</label>
    <input name="ad_title" value="<?php echo e($settings['ad_title']); ?>">
    <label>Mô tả quảng cáo</label>
    <textarea name="ad_body" rows="2"><?php echo e($settings['ad_body']); ?></textarea>
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <div class="sticky-save">
        <button class="btn" type="submit">Lưu cài đặt</button>
    </div>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
