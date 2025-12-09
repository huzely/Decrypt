<?php
require_once __DIR__ . '/includes/auth_check.php';
$page = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['search'] ?? '');
list($posts, $total) = get_paginated_posts($pdo, $page, 10, $search);
include __DIR__ . '/includes/header.php';
?>
<h1>Quản lý bài viết</h1>
<form method="get" style="margin-bottom:10px;">
    <input type="text" name="search" placeholder="Tìm theo tiêu đề" value="<?= escape_html($search); ?>">
    <button class="button" type="submit">Tìm</button>
</form>
<a class="button" href="/admin/post_add.php">Thêm bài</a>
<table class="admin-table">
    <tr><th>ID</th><th>Tiêu đề</th><th>Trạng thái</th><th>Clicks</th><th>Hành động</th></tr>
    <?php foreach ($posts as $post): $safe = escape_output($post, ['title','status']); ?>
        <tr>
            <td><?= (int)$post['id']; ?></td>
            <td><?= $safe['title']; ?></td>
            <td><?= $safe['status']; ?></td>
            <td><?= (int)$post['shopee_click_count']; ?></td>
            <td>
                <a href="/admin/post_edit.php?id=<?= (int)$post['id']; ?>">Sửa</a> |
                <a href="/admin/post_delete.php?id=<?= (int)$post['id']; ?>" onclick="return confirm('Xoá bài?');">Xoá</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php $totalPages = ceil($total / 10); if ($totalPages > 1): ?>
<div>
    <?php for ($i=1;$i<=$totalPages;$i++): ?>
        <a href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>" <?= $i===$page?'style="font-weight:bold;"':''; ?>><?= $i; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
