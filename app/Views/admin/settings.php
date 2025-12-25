<?php $settings = $settings ?? []; include __DIR__ . '/layout_top.php'; ?>
<form method="post" action="<?= base_url('admin/settings') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <h3>Thông tin trang</h3>
    <label>Tên trang<input type="text" name="site_name" value="<?= App\Core\Security::escape($settings['site_name'] ?? '') ?>"></label>
    <label>Mô tả<input type="text" name="site_description" value="<?= App\Core\Security::escape($settings['site_description'] ?? '') ?>"></label>
    <label>Footer<textarea name="footer_text"><?= App\Core\Security::escape($settings['footer_text'] ?? '') ?></textarea></label>
    <label>Logo<input type="file" name="logo"></label>
    <?php if (!empty($settings['logo'])): ?><input type="hidden" name="current_logo" value="<?= $settings['logo'] ?>"><?php endif; ?>
    <label>Banner<input type="file" name="banner"></label>
    <?php if (!empty($settings['banner'])): ?><input type="hidden" name="current_banner" value="<?= $settings['banner'] ?>"><?php endif; ?>
    <h3>Theme</h3>
    <select name="theme">
        <option value="theme-a" <?= ($settings['theme'] ?? '') === 'theme-a' ? 'selected' : '' ?>>Theme A</option>
        <option value="theme-b" <?= ($settings['theme'] ?? '') === 'theme-b' ? 'selected' : '' ?>>Theme B</option>
        <option value="theme-c" <?= ($settings['theme'] ?? '') === 'theme-c' ? 'selected' : '' ?>>Theme C</option>
    </select>
    <h3>Quảng cáo Shopee</h3>
    <label>Link Shopee<input type="url" name="shopee_link" value="<?= App\Core\Security::escape($settings['shopee_link'] ?? '') ?>"></label>
    <label><input type="checkbox" name="interstitial_enabled" <?= !empty($settings['interstitial_enabled']) ? 'checked' : '' ?>> Bật interstitial</label>
    <label>Tần suất<input type="number" name="interstitial_frequency" min="1" value="<?= App\Core\Security::escape($settings['interstitial_frequency'] ?? 1) ?>"></label>
    <label>Đơn vị
        <select name="interstitial_unit">
            <option value="session" <?= ($settings['interstitial_unit'] ?? '') === 'session' ? 'selected' : '' ?>>Mỗi session</option>
            <option value="hours" <?= ($settings['interstitial_unit'] ?? '') === 'hours' ? 'selected' : '' ?>>Mỗi N giờ</option>
            <option value="articles" <?= ($settings['interstitial_unit'] ?? '') === 'articles' ? 'selected' : '' ?>>Mỗi N bài</option>
        </select>
    </label>
    <h3>Anti-fraud</h3>
    <label>View cooldown (giây)<input type="number" name="view_cooldown_seconds" value="<?= App\Core\Security::escape($settings['view_cooldown_seconds'] ?? 120) ?>"></label>
    <label>Click cooldown (giây)<input type="number" name="click_cooldown_seconds" value="<?= App\Core\Security::escape($settings['click_cooldown_seconds'] ?? 300) ?>"></label>
    <label>Bot UA (mỗi dòng một mẫu)<textarea name="bot_list" rows="5"><?php echo isset($settings['bot_list']) ? App\Core\Security::escape(implode("\n", $settings['bot_list'])) : 'bot'; ?></textarea></label>
    <h3>Telegram</h3>
    <label><input type="checkbox" name="telegram_enabled" <?= !empty($settings['telegram_enabled']) ? 'checked' : '' ?>> Gửi khi xuất bản</label>
    <label><input type="checkbox" name="telegram_click_enabled" <?= !empty($settings['telegram_click_enabled']) ? 'checked' : '' ?>> Gửi khi click Shopee</label>
    <label>Bot token<input type="text" name="telegram_token" value="<?= App\Core\Security::escape($settings['telegram_token'] ?? '') ?>"></label>
    <label>Chat ID<input type="text" name="telegram_chat_id" value="<?= App\Core\Security::escape($settings['telegram_chat_id'] ?? '') ?>"></label>
    <button type="submit">Lưu</button>
</form>
<?php include __DIR__ . '/layout_bottom.php'; ?>
