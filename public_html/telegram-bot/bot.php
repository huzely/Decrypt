<?php
$config = require __DIR__ . '/../app/config/config.php';
require __DIR__ . '/../app/helpers/util.php';
require __DIR__ . '/../app/models/Article.php';
require __DIR__ . '/../app/models/SiteSettings.php';
require __DIR__ . '/../app/models/ClickEvent.php';

function tg_request(string $method, array $params = []) {
    global $config;
    $url = 'https://api.telegram.org/bot' . $config['telegram']['bot_token'] . '/' . $method;
    $opts = ['http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/json\r\n",
        'content' => json_encode($params),
        'timeout' => 5,
    ]];
    $context = stream_context_create($opts);
    return file_get_contents($url, false, $context);
}

function is_admin($userId): bool
{
    global $config;
    return in_array($userId, $config['telegram']['admin_ids']);
}

function handle_command(array $message)
{
    $chatId = $message['chat']['id'];
    $text = trim($message['text'] ?? '');
    $userId = $message['from']['id'] ?? 0;

    if (!is_admin($userId)) {
        tg_request('sendMessage', ['chat_id' => $chatId, 'text' => 'Không có quyền']);
        return;
    }

    if (strpos($text, '/stats') === 0) {
        $stats = ClickEvent::stats();
        $body = "Views: " . ($stats['article_view'] ?? 0) . "\nRedirect: " . ($stats['ad_forced_redirect'] ?? 0);
        tg_request('sendMessage', ['chat_id' => $chatId, 'text' => $body]);
        return;
    }

    if (strpos($text, '/reset') === 0) {
        ClickEvent::reset();
        tg_request('sendMessage', ['chat_id' => $chatId, 'text' => 'Đã reset click']);
        return;
    }

    if (strpos($text, '/add ') === 0) {
        $parts = explode('|', substr($text, 5));
        if (count($parts) >= 3) {
            $id = Article::create([
                'title' => $parts[0],
                'description' => $parts[1],
                'body' => $parts[2],
                'media_url' => $parts[3] ?? '',
                'is_public' => 1,
                'published_at' => date('Y-m-d H:i:s'),
            ]);
            tg_request('sendMessage', ['chat_id' => $chatId, 'text' => 'Đã tạo bài #' . $id]);
            return;
        }
    }

    if (strpos($text, '/ad ') === 0) {
        $parts = explode('|', substr($text, 4));
        SiteSettings::set([
            'ad_link' => $parts[0] ?? '',
            'ad_title' => $parts[1] ?? '',
            'ad_body' => $parts[2] ?? '',
        ]);
        tg_request('sendMessage', ['chat_id' => $chatId, 'text' => 'Đã cập nhật quảng cáo']);
        return;
    }

    tg_request('sendMessage', ['chat_id' => $chatId, 'text' => "Lệnh hỗ trợ:\n/stats, /reset, /add title|desc|body|media, /ad link|title|body"]);
}
