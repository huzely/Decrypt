<?php
$config = require __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/lib/db.php';
require_once __DIR__ . '/../../app/lib/helpers.php';
require_once __DIR__ . '/../../app/lib/posts.php';
require_once __DIR__ . '/../../app/lib/settings.php';
require_once __DIR__ . '/../../app/lib/tracking.php';

function tg_request(string $method, array $params = []) {
    global $config;
    $url = 'https://api.telegram.org/bot' . $config['TELEGRAM_BOT_TOKEN'] . '/' . $method;
    $opts = ['http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => json_encode($params),
        'timeout' => 5,
    ]];
    $context = stream_context_create($opts);
    return file_get_contents($url, false, $context);
}

function tg_send($chatId, $text, $keyboard = null) {
    $payload = ['chat_id' => $chatId, 'text' => $text, 'parse_mode' => 'HTML'];
    if ($keyboard) {
        $payload['reply_markup'] = $keyboard;
    }
    tg_request('sendMessage', $payload);
}

function is_admin_id($id): bool
{
    global $config;
    return in_array((int)$id, $config['TELEGRAM_ADMIN_IDS']);
}

function handle_update(array $update): void
{
    if (isset($update['message'])) {
        $msg = $update['message'];
        $chatId = $msg['chat']['id'];
        $userId = $msg['from']['id'] ?? 0;
        if (!is_admin_id($userId)) {
            tg_send($chatId, 'Bạn không có quyền.');
            return;
        }
        $text = trim($msg['text'] ?? '');
        route_command($chatId, $text);
    }
}

function route_command(int $chatId, string $text): void
{
    if (strpos($text, '/stats') === 0) {
        $s = tracking_stats();
        tg_send($chatId, "Views: " . ($s['article_view'] ?? 0) . "\nAd redirect: " . ($s['ad_forced_redirect'] ?? 0));
        return;
    }
    if (strpos($text, '/reset') === 0) {
        reset_tracking();
        tg_send($chatId, 'Đã reset thống kê');
        return;
    }
    if (strpos($text, '/add ') === 0) {
        $parts = explode('|', substr($text, 5));
        if (count($parts) >= 3) {
            $title = sanitize_text($parts[0]);
            $slug = unique_slug($title);
            $id = create_post([
                'title' => $title,
                'slug' => $slug,
                'excerpt' => sanitize_text($parts[1]),
                'content' => trim($parts[2]),
                'media_url' => $parts[3] ?? '',
                'is_public' => 1,
                'published_at' => date('Y-m-d H:i:s'),
            ]);
            tg_send($chatId, 'Đã tạo bài #' . $id . ' slug: ' . $slug);
            return;
        }
    }
    if (strpos($text, '/ad ') === 0) {
        $parts = explode('|', substr($text, 4));
        save_settings([
            'ad_link' => sanitize_text($parts[0] ?? ''),
            'ad_title' => sanitize_text($parts[1] ?? ''),
            'ad_body' => sanitize_text($parts[2] ?? ''),
        ] + get_settings());
        tg_send($chatId, 'Đã cập nhật quảng cáo');
        return;
    }
    tg_send($chatId, "Lệnh:\n/stats\n/reset\n/add title|excerpt|content|media\n/ad link|title|body");
}
