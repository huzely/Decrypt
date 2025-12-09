<?php
require_once __DIR__ . '/functions.php';

$postId = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
$post = fetch_post($pdo, $postId);
if (!$post || empty($post['shopee_link'])) {
    http_response_code(404);
    exit('Không tìm thấy link Shopee');
}

try {
    increment_shopee_click($pdo, $postId, $_SERVER['REMOTE_ADDR'] ?? 'unknown', $_SERVER['HTTP_USER_AGENT'] ?? '');
    notify_telegram("Bài #{$postId} có 1 click Shopee");
} catch (Exception $e) {
    // log silently
}

header('Location: ' . $post['shopee_link']);
exit;
