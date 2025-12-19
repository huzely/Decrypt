<?php
require __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../app/lib/slugify.php';

$search = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page-1)*$perPage;

$params = [];
$where = '';
if ($search) {
    $where = 'WHERE title LIKE ?';
    $params[] = "%{$search}%";
}
$stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM articles {$where} ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
foreach ($params as $i=>$v) {
    $stmt->bindValue($i+1, $v, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();
$total = (int)$pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
$pages = max(1, (int)ceil($total/$perPage));
?>
<h1>Bài viết</h1>
<a class="btn btn-primary" href="/admin/post_add.php">Thêm bài</a>
<form method="get" style="margin:12px 0;">
    <input class="form-control" name="q" placeholder="Tìm tiêu đề" value="<?= htmlspecialchars($search, ENT_QUOTES); ?>">
</form>
<table class="table">
    <thead><tr><th>ID</th><th>Tiêu đề</th><th>Slug</th><th>Trạng thái</th><th>Hành động</th></tr></thead>
    <tbody>
        <?php foreach ($articles as $a): ?>
            <tr>
                <td><?= (int)$a['id']; ?></td>
                <td><?= htmlspecialchars($a['title'], ENT_QUOTES); ?></td>
                <td><?= htmlspecialchars($a['slug'], ENT_QUOTES); ?></td>
                <td><?= htmlspecialchars($a['status'], ENT_QUOTES); ?></td>
                <td>
                    <a href="/admin/post_edit.php?id=<?= $a['id']; ?>">Sửa</a> |
                    <a href="/admin/post_delete.php?id=<?= $a['id']; ?>" onclick="return confirm('Xoá?');">Xoá</a> |
                    <a href="/admin/post_copy.php?id=<?= $a['id']; ?>">Copy</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php if ($pages>1): ?>
    <div class="pagination">
        <?php for($i=1;$i<=$pages;$i++): ?>
            <a href="?page=<?= $i; ?>&q=<?= urlencode($search); ?>" class="<?= $i===$page?'active':''; ?>"><?= $i; ?></a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
