<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/slugify.php';
require_auth();
$pageTitle = 'Bài viết';

$status = $_GET['status'] ?? 'all';
$query = "SELECT * FROM articles";
$params = [];
if (in_array($status, ['draft','public'])) {
    $query .= " WHERE status = :status";
    $params[':status'] = $status;
}
$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div class="topbar">
    <h1>Bài viết</h1>
    <div class="actions">
        <a class="btn" href="/admin/post_add.php">Thêm mới</a>
        <a class="btn" href="/admin/posts.php?status=public">Chỉ Public</a>
        <a class="btn" href="/admin/posts.php?status=draft">Chỉ Nháp</a>
        <a class="btn" href="/admin/posts.php">Tất cả</a>
    </div>
</div>
<div class="card">
    <table class="table">
        <thead>
            <tr><th>Tiêu đề</th><th>Slug</th><th>Trạng thái</th><th>Hành động</th></tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $p): ?>
                <tr>
                    <td><?php echo e($p['title']); ?></td>
                    <td><?php echo e($p['slug']); ?></td>
                    <td><?php echo e($p['status']); ?></td>
                    <td class="actions">
                        <a class="btn" href="/admin/post_edit.php?id=<?php echo $p['id']; ?>">Sửa</a>
                        <a class="btn" href="/admin/post_delete.php?id=<?php echo $p['id']; ?>" onclick="return confirm('Xóa bài này?');">Xóa</a>
                        <button class="btn copy" data-link="<?php echo e(current_url($p['slug'])); ?>">Copy link</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
document.querySelectorAll('.copy').forEach(btn => {
    btn.addEventListener('click', function(){
        navigator.clipboard.writeText(this.dataset.link).then(() => {
            alert('Đã copy link bài: ' + this.dataset.link);
        });
    });
});
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
