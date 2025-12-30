<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/track.php';

$pdo = get_pdo();
$type = $_POST['type'] ?? 'view';
$slug = $_POST['slug'] ?? '';
$postId = null;

if ($slug) {
    $stmt = $pdo->prepare('SELECT id FROM posts WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    if ($row) {
        $postId = (int)$row['id'];
    }
}

record_stat($pdo, $type === 'ad_click' ? 'ad_click' : 'view', $postId, 45);

echo json_encode(['status' => 'ok']);
