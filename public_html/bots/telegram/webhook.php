<?php
require_once __DIR__ . '/../../lib/error_handler.php';
require_once __DIR__ . '/../../lib/telegram.php';
$update = json_decode(file_get_contents('php://input'), true);
if (!$update) { exit('no data'); }
$message = $update['message']['text'] ?? '';
if ($message) {
    queueTelegramMessage('Bot nhận: ' . $message);
}
flushTelegramQueue();
echo 'ok';
