<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/posts.php';
$posts = fetch_posts(200, 0, false); // admin view top 200
include __DIR__ . '/includes/header.php';
?>
<section class="card admin-card">
    <div class="flex-between">
        <h2>Bài viết</h2>
        <div>
            <a class="btn" href="/admin/post_add.php">Thêm bài</a>
        </div>
    </div>
    <table class="table">
        <thead><tr><th>ID</th><th>Tiêu đề</th><th>Slug</th><th>Ngày</th><th>TT</th><th>Hành động</th></tr></thead>
        <tbody>
        <?php foreach ($posts as $p): ?>
            <tr>
                <td><?php echo (int)$p['id']; ?></td>
                <td><?php echo e($p['title']); ?></td>
                <td><?php echo e($p['slug']); ?></td>
                <td><?php echo e($p['published_at']); ?></td>
                <td><?php echo $p['is_public'] ? 'Public' : 'Ẩn'; ?></td>
                <td>
                    <a class="btn" href="/admin/post_edit.php?id=<?php echo (int)$p['id']; ?>">Sửa</a>
                    <a class="btn" href="/admin/post_delete.php?id=<?php echo (int)$p['id']; ?>&csrf=<?php echo e(csrf_token()); ?>" onclick="return confirm('Xoá?');">Xoá</a>
                    <a class="btn" href="/admin/post_add.php?copy=<?php echo (int)$p['id']; ?>">Copy</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
