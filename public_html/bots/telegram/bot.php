<?php
require __DIR__ . '/../../app/config/config.php';
require __DIR__ . '/../../app/lib/db.php';
require __DIR__ . '/keyboard.php';

$pdo = db($config);
$BOT = $config['telegram_bot_token'];
$API = "https://api.telegram.org/bot{$BOT}/";

function tg_request(string $method, array $params): array
{
    global $API;
    $ch = curl_init($API . $method);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true) ?: [];
}

function tg_send($chatId, $text, $kb = null) {
    $params = ['chat_id'=>$chatId, 'text'=>$text, 'parse_mode'=>'HTML'];
    if ($kb) $params['reply_markup'] = json_encode($kb);
    tg_request('sendMessage', $params);
}

function is_allowed($chatId, array $config): bool
{
    return in_array((int)$chatId, array_map('intval', $config['telegram_admin_ids']), true);
}

function log_admin(PDO $pdo, int $userId, string $action, array $payload = []): void
{
    $stmt = $pdo->prepare('INSERT INTO admin_logs (telegram_user_id, action, payload, created_at) VALUES (?,?,?,NOW())');
    $stmt->execute([$userId, $action, json_encode($payload)]);
}
