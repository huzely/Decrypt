<?php
require_once __DIR__ . '/includes/header.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['q'] ?? '');
[$posts, $total] = get_paginated_posts($pdo, $page, 10, $search);
$total_pages = (int)ceil($total / 10);
?>
<div class="admin-header">
    <h2>Quản lý bài viết</h2>
    <a href="/admin/post_add.php">+ Thêm bài viết</a>
</div>
<form method="get" style="margin-bottom:10px;">
    <input type="text" name="q" placeholder="Tìm theo tiêu đề" value="<?php echo escape_html($search); ?>">
    <button type="submit">Tìm kiếm</button>
</form>
<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Loại</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($posts as $post): ?>
            <tr>
                <td><?php echo (int)$post['id']; ?></td>
                <td><?php echo escape_html($post['title']); ?></td>
                <td><?php echo escape_html($post['type']); ?></td>
                <td><?php echo escape_html($post['status']); ?></td>
                <td>
                    <a href="/post.php?id=<?php echo (int)$post['id']; ?>" target="_blank">Xem</a> |
                    <a href="/admin/post_edit.php?id=<?php echo (int)$post['id']; ?>">Sửa</a> |
                    <a href="/admin/post_delete.php?id=<?php echo (int)$post['id']; ?>" onclick="return confirm('Xoá hẳn bài viết này?');">Xoá</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&q=<?php echo urlencode($search); ?>" <?php if ($i === $page) echo 'style="font-weight:bold;"'; ?>><?php echo $i; ?></a>
    <?php endfor; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
