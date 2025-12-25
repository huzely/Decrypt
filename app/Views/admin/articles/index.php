<?php $settings = $settings ?? []; include __DIR__ . '/../layout_top.php'; ?>
<div class="actions"><a href="<?= base_url('admin/articles/create') ?>" class="btn">Thêm bài</a></div>
<table>
    <tr><th>Tiêu đề</th><th>Slug</th><th>Trạng thái</th><th>Ngày</th><th>Hành động</th></tr>
    <?php foreach ($articles as $article): ?>
        <tr>
            <td><?= App\Core\Security::escape($article['title']) ?></td>
            <td><?= App\Core\Security::escape($article['slug']) ?></td>
            <td><?= $article['status'] ?></td>
            <td><?= human_date($article['published_at']) ?></td>
            <td>
                <a href="<?= base_url('admin/articles/edit/' . $article['id']) ?>">Sửa</a>
                <form action="<?= base_url('admin/articles/delete/' . $article['id']) ?>" method="post" style="display:inline" onsubmit="return confirm('Xóa?')">
                    <?= csrf_field() ?>
                    <button type="submit">Xóa</button>
                </form>
                <form action="<?= base_url('admin/articles/copy/' . $article['id']) ?>" method="post" style="display:inline">
                    <?= csrf_field() ?>
                    <button type="submit">Copy</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php include __DIR__ . '/../layout_bottom.php'; ?>
