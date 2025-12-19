<?php
$config = require __DIR__ . '/../app/config/config.php';
require __DIR__ . '/../app/helpers/util.php';
require __DIR__ . '/../app/models/ClickEvent.php';

header('Content-Type: application/json');

$type = $_POST['type'] ?? $_GET['type'] ?? '';
$articleId = (int)($_POST['article_id'] ?? $_GET['article_id'] ?? 0);
$token = $_POST['token'] ?? $_GET['token'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if (!$type || !$articleId || $token !== session_token()) {
    http_response_code(400);
    echo json_encode(['status' => 'error']);
    exit;
}

// Rate limit per IP
if (ClickEvent::countRecent($type, $ip, 15) > 5) {
    echo json_encode(['status' => 'limited']);
    exit;
}

ClickEvent::record($type, $articleId, $ip, $token);

echo json_encode(['status' => 'ok']);
