<?php
require_once __DIR__ . '/db.php';
$config = require __DIR__ . '/../config.php';

function sendTelegramMessage(string $message): void {
    global $config;
    if (empty($config['TELEGRAM_BOT_TOKEN']) || empty($config['TELEGRAM_ADMIN_CHAT_ID'])) {
        return;
    }
    $url = 'https://api.telegram.org/bot' . $config['TELEGRAM_BOT_TOKEN'] . '/sendMessage';
    $data = [
        'chat_id' => $config['TELEGRAM_ADMIN_CHAT_ID'],
        'text' => $message,
    ];
    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
            'timeout' => 3,
        ],
    ];
    @file_get_contents($url, false, stream_context_create($options));
}

function queueTelegramMessage(string $message): void {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO telegram_queue(message, created_at) VALUES(:msg, NOW())');
    $stmt->execute([':msg' => $message]);
}

function flushTelegramQueue(): void {
    global $pdo;
    $config = require __DIR__ . '/../config.php';
    if (empty($config['TELEGRAM_BOT_TOKEN']) || empty($config['TELEGRAM_ADMIN_CHAT_ID'])) {
        return;
    }
    $stmt = $pdo->query('SELECT * FROM telegram_queue ORDER BY id ASC LIMIT 5');
    $rows = $stmt->fetchAll();
    foreach ($rows as $row) {
        sendTelegramMessage($row['message']);
        $del = $pdo->prepare('DELETE FROM telegram_queue WHERE id = :id');
        $del->execute([':id' => $row['id']]);
    }
}

function notifyAdClick(string $slug): void {
    global $pdo;
    $row = $pdo->query("SELECT * FROM telegram_notifications WHERE event_type='ad_click' LIMIT 1")->fetch();
    if (!$row) {
        $pdo->prepare("INSERT INTO telegram_notifications(event_type, counter, last_sent_at) VALUES('ad_click',1,NOW())")->execute();
        return;
    }
    $counter = (int)$row['counter'] + 1;
    $shouldSend = ($counter >= 10) || (strtotime($row['last_sent_at']) < strtotime('-5 minutes'));
    if ($shouldSend) {
        queueTelegramMessage('Có ' . $counter . ' click quảng cáo mới - Slug: ' . $slug);
        $pdo->prepare("UPDATE telegram_notifications SET counter=0, last_sent_at=NOW() WHERE event_type='ad_click'")->execute();
    } else {
        $stmt = $pdo->prepare("UPDATE telegram_notifications SET counter=:c WHERE event_type='ad_click'");
        $stmt->execute([':c' => $counter]);
    }
}
