<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/db.php';
include __DIR__ . '/includes/header.php';
$articles = db()->query('SELECT * FROM articles ORDER BY created_at DESC')->fetchAll();
?>
<div class="actions"><a class="btn" href="<?= BASE_URL ?>/admin/post_add.php">Thêm bài</a></div>
<table>
    <tr><th>Tiêu đề</th><th>Slug</th><th>Trạng thái</th><th>Hành động</th></tr>
    <?php foreach ($articles as $a): ?>
    <tr>
        <td><?= htmlspecialchars($a['title']) ?></td>
        <td><?= htmlspecialchars($a['slug']) ?></td>
        <td><?= $a['status'] ?></td>
        <td>
            <a href="<?= BASE_URL ?>/admin/post_edit.php?id=<?= $a['id'] ?>">Sửa</a> |
            <a href="<?= BASE_URL ?>/admin/post_copy.php?id=<?= $a['id'] ?>">Copy</a> |
            <a href="<?= BASE_URL ?>/admin/post_delete.php?id=<?= $a['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php include __DIR__ . '/includes/footer.php'; ?>
