<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db.php';

function sendTelegramMessage(string $text): void {
    if (!TELEGRAM_BOT_TOKEN || !TELEGRAM_ADMIN_CHAT_ID) return;
    $url = 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage';
    $payload = ['chat_id' => TELEGRAM_ADMIN_CHAT_ID, 'text' => $text];
    @file_get_contents($url . '?' . http_build_query($payload));
}

function queue_click_notify(int $count): void {
    $stmt = db()->prepare('INSERT INTO telegram_queue (type, counter, last_sent_at) VALUES ("click", :cnt, NOW()) ON DUPLICATE KEY UPDATE counter = counter + :cnt');
    $stmt->execute([':cnt' => $count]);
}

function flush_telegram_queue(): void {
    $stmt = db()->query('SELECT * FROM telegram_queue');
    foreach ($stmt->fetchAll() as $row) {
        if ($row['type'] === 'click' && strtotime($row['last_sent_at']) < time() - 300 && $row['counter'] > 0) {
            sendTelegramMessage('Có ' . $row['counter'] . ' click Shopee mới.');
            db()->prepare('UPDATE telegram_queue SET counter = 0, last_sent_at = NOW() WHERE id = :id')->execute([':id' => $row['id']]);
        }
    }
}
