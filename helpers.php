<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function ensure_default_admin(): void
{
    try {
        $pdo = get_pdo();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->execute(['admin']);
        if (!$stmt->fetch()) {
            $hash = password_hash('123456', PASSWORD_BCRYPT);
            $insert = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
            $insert->execute(['admin', $hash]);
        }
    } catch (Throwable $e) {
        // Bỏ qua khi chưa kết nối được DB; sẽ thử lại ở lần gọi tiếp theo
    }
}

function sanitize(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function fetch_settings(): array
{
    $defaults = [
        'site_title' => 'Shopee → Telegram wrapper',
        'hero_title' => 'Danh sách link đã bọc',
        'hero_subtitle' => 'Mở Shopee, sau 5 giây tự qua Telegram',
        'primary_color' => '#6366f1',
        'accent_color' => '#f59e0b',
        'admin_note' => 'Chào mừng đến trang quản trị',
    ];

    try {
        $pdo = get_pdo();
        $rows = $pdo->query('SELECT `key`, `value` FROM settings')->fetchAll();
        foreach ($rows as $row) {
            $defaults[$row['key']] = $row['value'];
        }
    } catch (Throwable $e) {
        // Khi chưa migrate DB hoặc lỗi kết nối, dùng mặc định
    }

    return $defaults;
}

function set_setting(string $key, string $value): void
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');
    $stmt->execute([$key, $value]);
}

function detect_browser(string $ua): string
{
    $ua = strtolower($ua);
    if (strpos($ua, 'edge') !== false) return 'Edge';
    if (strpos($ua, 'edg') !== false) return 'Edge';
    if (strpos($ua, 'chrome') !== false) return 'Chrome';
    if (strpos($ua, 'safari') !== false && strpos($ua, 'chrome') === false) return 'Safari';
    if (strpos($ua, 'firefox') !== false) return 'Firefox';
    if (strpos($ua, 'opera') !== false || strpos($ua, 'opr/') !== false) return 'Opera';
    if (strpos($ua, 'msie') !== false || strpos($ua, 'trident') !== false) return 'Internet Explorer';
    return 'Unknown';
}

function is_bot_user_agent(string $ua): bool
{
    $ua = strtolower(trim($ua));
    if ($ua === '') {
        return true;
    }

    $bots = [
        'bot', 'crawl', 'spider', 'slurp', 'headless', 'facebookexternalhit', 'telegrambot', 'preview',
        'phantom', 'curl', 'wget', 'python-requests', 'httpclient'
    ];

    foreach ($bots as $marker) {
        if (strpos($ua, $marker) !== false) {
            return true;
        }
    }

    return false;
}

function detect_os(string $ua): string
{
    $ua = strtolower($ua);
    if (strpos($ua, 'windows nt 10') !== false) return 'Windows 10';
    if (strpos($ua, 'windows nt 6.3') !== false) return 'Windows 8.1';
    if (strpos($ua, 'windows nt 6.2') !== false) return 'Windows 8';
    if (strpos($ua, 'windows nt 6.1') !== false) return 'Windows 7';
    if (strpos($ua, 'mac os') !== false || strpos($ua, 'macintosh') !== false) return 'macOS';
    if (strpos($ua, 'android') !== false) return 'Android';
    if (strpos($ua, 'iphone') !== false || strpos($ua, 'ipad') !== false) return 'iOS';
    if (strpos($ua, 'linux') !== false) return 'Linux';
    return 'Unknown';
}

function send_telegram_message(string $text): void
{
    if (!TELEGRAM_BOT_TOKEN || !TELEGRAM_CHAT_ID) {
        return;
    }

    $url = 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage';
    $payload = http_build_query([
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $text,
    ]);

    @file_get_contents($url . '?' . $payload);
}

function record_click(array $link): array
{
    $pdo = get_pdo();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Bỏ qua bot/crawler để thống kê và thông báo chỉ dùng dữ liệu người thật
    if (is_bot_user_agent($ua)) {
        return [];
    }

    $browser = detect_browser($ua);
    $os = detect_os($ua);

    $check = $pdo->prepare('SELECT COUNT(*) FROM clicks WHERE link_id = ? AND ip_address = ?');
    $check->execute([$link['id'], $ip]);
    $nth = ((int)$check->fetchColumn()) + 1;

    $stmt = $pdo->prepare('INSERT INTO clicks (link_id, ip_address, user_agent, browser, os, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$link['id'], $ip, $ua, $browser, $os]);

    return [
        'ip' => $ip,
        'browser' => $browser,
        'os' => $os,
        'nth' => $nth,
    ];
}

function ensure_slug(string $desired, string $fallback): string
{
    $slug = strtolower(trim($desired));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug ?: preg_replace('/[^a-z0-9-]/', '-', strtolower($fallback));
}

function link_meta_image_path(?string $filename): ?string
{
    if (!$filename) return null;
    return UPLOAD_BASE_URL . '/' . $filename;
}
