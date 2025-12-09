<?php
require_once __DIR__ . '/functions.php';

$input = file_get_contents('php://input');
$update = json_decode($input, true);
if (!$update || !isset($update['message'])) {
    exit('ok');
}

$message = $update['message'];
$chatId = $message['chat']['id'] ?? null;
$text = trim($message['text'] ?? '');

$stmt = $pdo->prepare('SELECT * FROM telegram_admins WHERE telegram_chat_id = :id AND is_active = 1');
$stmt->execute([':id' => $chatId]);
$admin = $stmt->fetch();
if (!$admin) {
    exit('unauthorized');
}

function send_reply($chatId, $text)
{
    global $telegramApiUrl;
    $payload = ['chat_id' => $chatId, 'text' => $text, 'parse_mode' => 'HTML'];
    $ch = curl_init("{$telegramApiUrl}/sendMessage");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

$keyboard = [
    'keyboard' => [
        [['text' => '/stats_today'], ['text' => '/stats_total']],
        [['text' => '/top_posts'], ['text' => '/post']],
        [['text' => '/publish'], ['text' => '/unpublish']],
        [['text' => '/help']],
    ],
    'resize_keyboard' => true,
];

if (in_array($text, ['/start', '/help', '/menu'], true)) {
    send_reply($chatId, "Chào {$admin['name']}! Chọn chức năng.");
    $ch = curl_init("{$telegramApiUrl}/sendMessage");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['chat_id' => $chatId, 'text' => 'Menu', 'reply_markup' => json_encode($keyboard)]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
    exit;
}

if ($text === '/stats_today') {
    $s = get_daily_stats($pdo);
    send_reply($chatId, "Clicks hôm nay: {$s['clicks_today']}\nBài publish hôm nay: {$s['published_today']}");
    exit;
}

if ($text === '/stats_total') {
    $clicks = (int)$pdo->query('SELECT COUNT(*) FROM click_logs')->fetchColumn();
    $published = (int)$pdo->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
    send_reply($chatId, "Tổng clicks: {$clicks}\nTổng bài publish: {$published}");
    exit;
}

if ($text === '/top_posts') {
    $rows = get_top_posts($pdo, 5);
    $lines = array_map(function($r){return "#{$r['id']} - {$r['title']} ({$r['shopee_click_count']} click)";}, $rows);
    send_reply($chatId, implode("\n", $lines));
    exit;
}

if (strpos($text, '/post') === 0) {
    $parts = explode(' ', $text, 2);
    if (isset($parts[1])) {
        $id = (int)$parts[1];
        $post = fetch_post($pdo, $id);
        if ($post) {
            send_reply($chatId, "#{$post['id']} - {$post['title']} (status: {$post['status']})");
        } else {
            send_reply($chatId, 'Không tìm thấy bài');
        }
    } else {
        send_reply($chatId, 'Dùng: /post ID');
    }
    exit;
}

if (strpos($text, '/publish') === 0 || strpos($text, '/unpublish') === 0) {
    $parts = explode(' ', $text, 2);
    if (!isset($parts[1])) {
        send_reply($chatId, 'Dùng: /publish ID hoặc /unpublish ID');
        exit;
    }
    $id = (int)$parts[1];
    $status = strpos($text, '/publish') === 0 ? 'published' : 'draft';
    $stmt = $pdo->prepare('UPDATE posts SET status=:status, updated_at=NOW() WHERE id=:id');
    $stmt->execute([':status' => $status, ':id' => $id]);
    send_reply($chatId, "Đã cập nhật bài #{$id} thành {$status}");
    exit;
}

send_reply($chatId, 'Không hiểu lệnh');
