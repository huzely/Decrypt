<?php
require_once __DIR__ . '/functions.php';
header('Content-Type: application/json');
$postId = (int)($_GET['post_id'] ?? 0);
if ($postId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid post id']);
    exit;
}
$token = create_ad_token($postId);
echo json_encode($token);
