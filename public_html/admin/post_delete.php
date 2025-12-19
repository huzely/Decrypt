<?php
require __DIR__ . '/includes/header.php';
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare('DELETE FROM articles WHERE id=? LIMIT 1');
    $stmt->execute([$id]);
    cache_clear($config);
}
header('Location: /admin/posts.php');
exit;
