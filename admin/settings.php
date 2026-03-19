<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = site_settings();
    $stmt = $pdo->prepare('UPDATE settings SET site_name=:site_name, logo=:logo, primary_color=:primary_color, popup_ads_enabled=:popup_ads_enabled WHERE id=:id');
    $stmt->execute(['site_name' => trim($_POST['site_name']), 'logo' => trim($_POST['logo']), 'primary_color' => trim($_POST['primary_color']), 'popup_ads_enabled' => !empty($_POST['popup_ads_enabled']) ? 1 : 0, 'id' => $current['id']]);
    header('Location: settings.php'); exit;
}
$settings = site_settings();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Settings</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="admin-layout"><?php include __DIR__ . '/partials/sidebar.php'; ?><main class="admin-main"><section class="panel"><h1>Settings</h1><form method="post" class="admin-form"><label>Site Name<input name="site_name" value="<?php echo esc($settings['site_name']); ?>" required></label><label>Logo URL<input name="logo" value="<?php echo esc($settings['logo']); ?>"></label><label>Primary Color<input name="primary_color" value="<?php echo esc($settings['primary_color']); ?>" required></label><label class="inline-check"><input type="checkbox" name="popup_ads_enabled" value="1" <?php echo !empty($settings['popup_ads_enabled']) ? 'checked' : ''; ?>> Enable Popup Ads</label><button class="btn-primary" type="submit">Save Settings</button></form></section></main></div></body></html>
