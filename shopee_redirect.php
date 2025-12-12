<?php
require_once __DIR__ . '/functions.php';

$postId = (int)($_GET['post_id'] ?? 0);
$token = $_GET['token'] ?? '';
if ($postId <= 0) {
    http_response_code(400);
    echo 'Thiếu post_id';
    exit;
}

$postStmt = $pdo->prepare('SELECT * FROM posts WHERE id = :id');
$postStmt->execute([':id' => $postId]);
$post = $postStmt->fetch();
if (!$post || empty($post['shopee_link'])) {
    echo 'Bài viết không tồn tại hoặc không có link Shopee';
    exit;
}

$ip = substr($_SERVER['REMOTE_ADDR'] ?? 'unknown', 0, 100);
$ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255);
$validation = validate_click_token($postId, $token, $ip, $ua);
$isValid = $validation['valid'];
$reason = $validation['reason'] ?? null;
$tokenId = $validation['token_id'] ?? null;
if ($tokenId === null) {
    $tokenId = 0;
}

mark_click_valid($postId, $tokenId, $ip, $ua, $isValid, $reason);
if ($isValid) {
    send_telegram_message("Bài #{$postId} có 1 click Shopee hợp lệ");
} else {
    // vẫn redirect để trải nghiệm người dùng mượt mà
}

header('Location: ' . $post['shopee_link']);
exit;
