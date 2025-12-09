<?php
// Xoá hẳn bài viết (hard delete). Có thể chuyển sang soft delete bằng cách cập nhật status nếu cần.
require_once __DIR__ . '/includes/header.php';
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('DELETE FROM posts WHERE id = :id');
$stmt->execute([':id' => $id]);
redirect_with_message('/admin/posts.php', 'Đã xoá bài viết');
?>
