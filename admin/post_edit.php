<?php include __DIR__ . '/includes/header.php'; ?>
<?php
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id=:id');
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();
if (!$post) { echo 'Không tìm thấy'; exit; }
$oldStatus = $post['status'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $slugInput = $_POST['slug'] ?? slugify($title);
    $slug = ensure_unique_slug($pdo, $slugInput, $id);
    $content = $_POST['content'] ?? '';
    $telegram_image_url = $_POST['telegram_image_url'] ?? '';
    $telegram_video_url = $_POST['telegram_video_url'] ?? '';
    $thumbnail = $_POST['thumbnail'] ?? '';
    $meta_image = $_POST['meta_image'] ?? '';
    $shopee_link = $_POST['shopee_link'] ?? '';
    $external_url = $_POST['external_url'] ?? '';
    $meta_title = $_POST['meta_title'] ?? $title;
    $meta_description = $_POST['meta_description'] ?? '';
    $meta_keywords = $_POST['meta_keywords'] ?? '';
    $type = $_POST['type'] ?? 'article';
    $status = $_POST['status'] ?? 'draft';

    $stmtUpdate = $pdo->prepare('UPDATE posts SET slug=:slug,title=:title,content=:content,thumbnail=:thumbnail,telegram_image_url=:ti,telegram_video_url=:tv,shopee_link=:shopee,external_url=:ext,meta_title=:mt,meta_description=:md,meta_keywords=:mk,meta_image=:mi,type=:type,status=:status,updated_at=NOW() WHERE id=:id');
    $stmtUpdate->execute([
        ':slug' => $slug,
        ':title' => $title,
        ':content' => $content,
        ':thumbnail' => $thumbnail,
        ':ti' => $telegram_image_url,
        ':tv' => $telegram_video_url,
        ':shopee' => $shopee_link,
        ':ext' => $external_url,
        ':mt' => $meta_title,
        ':md' => $meta_description,
        ':mk' => $meta_keywords,
        ':mi' => $meta_image,
        ':type' => $type,
        ':status' => $status,
        ':id' => $id,
    ]);
    if ($oldStatus === 'draft' && $status === 'published') {
        publish_post_notify(['id'=>$id,'title'=>$title,'slug'=>$slug]);
    }
    redirect_with_message('/admin/posts.php','Đã cập nhật');
}
?>
<h1>Sửa bài</h1>
<form method="post">
    <div class="form-group"><label>Tiêu đề</label><input name="title" value="<?= escape_html($post['title']); ?>" required></div>
    <div class="form-group"><label>Slug</label><input name="slug" value="<?= escape_html($post['slug']); ?>"></div>
    <div class="form-group"><label>Loại</label><select name="type">
        <option value="article" <?= $post['type']==='article'?'selected':''; ?>>Article</option>
        <option value="announcement" <?= $post['type']==='announcement'?'selected':''; ?>>Announcement</option>
        <option value="video" <?= $post['type']==='video'?'selected':''; ?>>Video</option>
    </select></div>
    <div class="form-group"><label>Trạng thái</label><select name="status">
        <option value="draft" <?= $post['status']==='draft'?'selected':''; ?>>Draft</option>
        <option value="published" <?= $post['status']==='published'?'selected':''; ?>>Published</option>
    </select></div>
    <div class="form-group"><label>Thumbnail URL</label><input name="thumbnail" value="<?= escape_html($post['thumbnail']); ?>"></div>
    <div class="form-group"><label>Meta Image</label><input name="meta_image" value="<?= escape_html($post['meta_image']); ?>"></div>
    <div class="form-group"><label>Telegram image URL</label><input name="telegram_image_url" value="<?= escape_html($post['telegram_image_url']); ?>"></div>
    <div class="form-group"><label>Telegram video URL</label><input name="telegram_video_url" value="<?= escape_html($post['telegram_video_url']); ?>"></div>
    <div class="form-group"><label>Shopee link</label><input name="shopee_link" value="<?= escape_html($post['shopee_link']); ?>"></div>
    <div class="form-group"><label>External URL</label><input name="external_url" value="<?= escape_html($post['external_url']); ?>"></div>
    <div class="form-group"><label>Meta title</label><input name="meta_title" value="<?= escape_html($post['meta_title']); ?>"></div>
    <div class="form-group"><label>Meta description</label><textarea name="meta_description"><?= escape_html($post['meta_description']); ?></textarea></div>
    <div class="form-group"><label>Meta keywords</label><input name="meta_keywords" value="<?= escape_html($post['meta_keywords']); ?>"></div>
    <div class="form-group"><label>Nội dung</label><textarea name="content" rows="8"><?= escape_html($post['content']); ?></textarea></div>
    <button class="btn primary" type="submit">Lưu</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
