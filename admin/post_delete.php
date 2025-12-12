<?php include __DIR__ . '/includes/header.php'; ?>
<?php
$id = (int)($_GET['id'] ?? 0);
$pdo->prepare('DELETE FROM posts WHERE id=:id')->execute([':id' => $id]);
redirect_with_message('/admin/posts.php','Đã xoá');
?>
