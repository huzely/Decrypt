<?php $settings = $settings ?? []; include __DIR__ . '/../layout_top.php'; ?>
<form method="post" action="<?= base_url('admin/articles/' . ($article ? 'update/' . $article['id'] : 'store')) ?>">
    <?= csrf_field() ?>
    <?php if ($article): ?><input type="hidden" name="article_id" value="<?= $article['id'] ?>"><?php endif; ?>
    <label>Tiêu đề<input type="text" name="title" value="<?= App\Core\Security::escape($article['title'] ?? '') ?>" required></label>
    <label>Slug<input type="text" name="slug" value="<?= App\Core\Security::escape($article['slug'] ?? '') ?>"></label>
    <label>Meta title<input type="text" name="meta_title" value="<?= App\Core\Security::escape($article['meta_title'] ?? '') ?>"></label>
    <label>Meta description<textarea name="meta_description"><?= App\Core\Security::escape($article['meta_description'] ?? '') ?></textarea></label>
    <label>Keywords<input type="text" name="keywords" value="<?= App\Core\Security::escape($article['keywords'] ?? '') ?>"></label>
    <label>OG image URL<input type="text" name="og_image" value="<?= App\Core\Security::escape($article['og_image'] ?? '') ?>"></label>
    <label>Danh mục
        <select name="category_id">
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (!empty($article['category_id']) && $article['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= App\Core\Security::escape($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Tags (phân cách bởi dấu phẩy)<input type="text" name="tags" value="<?= App\Core\Security::escape($article['tags'] ?? '') ?>"></label>
    <label>Nội dung (cho phép HTML/iframe Telegram)<textarea name="content" rows="10"><?= App\Core\Security::escape($article['content'] ?? '') ?></textarea></label>
    <label>Trạng thái
        <select name="status">
            <option value="draft" <?= ($article['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="published" <?= ($article['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
        </select>
    </label>
    <label>Thêm đuôi id vào slug<input type="checkbox" name="include_id_tail" value="1" <?= !empty($article['include_id_tail']) ? 'checked' : '' ?>></label>
    <label>Ngày đăng<input type="datetime-local" name="published_at" value="<?= isset($article['published_at']) ? date('Y-m-d\TH:i', strtotime($article['published_at'])) : '' ?>"></label>
    <button type="submit">Lưu</button>
</form>
<?php include __DIR__ . '/../layout_bottom.php'; ?>
