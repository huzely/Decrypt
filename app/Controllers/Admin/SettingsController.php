<?php
namespace App\Controllers\Admin;

use App\Core\Security;
use App\Core\View;
use App\Models\Setting;

class SettingsController extends BaseAdminController
{
    public function index(): void
    {
        $this->requireAuth();
        $settings = Setting::getAll();
        View::render('admin/settings', ['settings' => $settings]);
    }

    public function save(): void
    {
        $this->requireAuth();
        verify_csrf();
        $data = [
            'site_name' => $_POST['site_name'] ?? 'Tin tức',
            'site_description' => $_POST['site_description'] ?? '',
            'footer_text' => $_POST['footer_text'] ?? '',
            'theme' => $_POST['theme'] ?? 'theme-a',
            'shopee_link' => $_POST['shopee_link'] ?? '',
            'interstitial_enabled' => !empty($_POST['interstitial_enabled']),
            'interstitial_frequency' => (int)($_POST['interstitial_frequency'] ?? 1),
            'interstitial_unit' => $_POST['interstitial_unit'] ?? 'session',
            'bot_list' => array_filter(array_map('trim', explode("\n", $_POST['bot_list'] ?? 'bot'))),
            'view_cooldown_seconds' => (int)($_POST['view_cooldown_seconds'] ?? 120),
            'click_cooldown_seconds' => (int)($_POST['click_cooldown_seconds'] ?? 300),
            'telegram_enabled' => !empty($_POST['telegram_enabled']),
            'telegram_click_enabled' => !empty($_POST['telegram_click_enabled']),
            'telegram_token' => $_POST['telegram_token'] ?? '',
            'telegram_chat_id' => $_POST['telegram_chat_id'] ?? '',
            'logo' => $this->handleUpload('logo', $_POST['current_logo'] ?? ''),
            'banner' => $this->handleUpload('banner', $_POST['current_banner'] ?? ''),
        ];
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }
        $_SESSION['flash'] = 'Đã lưu cài đặt';
        redirect('admin/settings');
    }

    private function handleUpload(string $field, string $current): string
    {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            return $current;
        }
        $file = $_FILES[$field];
        if ($file['size'] > 2 * 1024 * 1024) {
            return $current;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) {
            return $current;
        }
        $ext = $allowed[$mime];
        $name = Security::sanitizeFilename($field . '-' . time() . '.' . $ext);
        $path = __DIR__ . '/../../../public/uploads/' . $name;
        move_uploaded_file($file['tmp_name'], $path);
        return 'uploads/' . $name;
    }
}
