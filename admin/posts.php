<?php include __DIR__ . '/includes/header.php'; ?>
<?php
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10; $offset = ($page-1)*$limit;
$where = '';
$params = [];
if ($search) {
    $where = "WHERE title LIKE :s";
    $params[':s'] = "%{$search}%";
}
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM posts {$where}");
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();
$stmt = $pdo->prepare("SELECT * FROM posts {$where} ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}");
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>
<h1>Bài viết</h1>
<form method="get" style="margin-bottom:10px;display:flex;gap:8px;">
    <input name="search" value="<?= escape_html($search); ?>" placeholder="Tìm tiêu đề">
    <button class="btn primary" type="submit">Tìm</button>
    <a class="btn" href="/admin/post_add.php">+ Thêm</a>
</form>
<table>
    <thead><tr><th>ID</th><th>Tiêu đề</th><th>Slug</th><th>Loại</th><th>Trạng thái</th><th>Click</th><th>Hành động</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= (int)$row['id']; ?></td>
            <td><?= escape_html($row['title']); ?></td>
            <td><?= escape_html($row['slug']); ?></td>
            <td><?= escape_html($row['type']); ?></td>
            <td><?= escape_html($row['status']); ?></td>
            <td><?= (int)$row['shopee_click_count']; ?></td>
            <td>
                <a href="/admin/post_edit.php?id=<?= (int)$row['id']; ?>">Sửa</a> |
                <a href="/admin/post_delete.php?id=<?= (int)$row['id']; ?>" onclick="return confirm('Xoá?');">Xoá</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div style="margin-top:10px;">
    Trang:
    <?php for ($i=1;$i<=ceil($total/$limit);$i++): ?>
        <a href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>" style="margin-right:6px;<?= $i==$page?'font-weight:bold;':'' ?>"><?= $i; ?></a>
    <?php endfor; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
