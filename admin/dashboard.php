<?php
require_once __DIR__ . '/includes/header.php';

$totalStmt = $pdo->query('SELECT COUNT(*) FROM posts');
$totalPosts = (int)$totalStmt->fetchColumn();
?>
<h2>Tổng quan</h2>
<p>Hiện có <?php echo $totalPosts; ?> bài viết trong hệ thống.</p>
<p><a href="/admin/posts.php">Quản lý bài viết</a></p>
<?php include __DIR__ . '/includes/footer.php'; ?>
