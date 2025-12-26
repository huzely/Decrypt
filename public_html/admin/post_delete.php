<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/db.php';
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $stmt = db()->prepare('DELETE FROM articles WHERE id=:id');
    $stmt->execute([':id'=>$id]);
}
header('Location: '.BASE_URL.'/admin/posts.php');
