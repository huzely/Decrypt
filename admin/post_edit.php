<?php
require_once __DIR__ . '/includes/header.php';
$id = (int)($_GET['id'] ?? 0);
$post = fetch_post($pdo, $id);
if (!$post) {
    redirect_with_message('/admin/posts.php', 'Không tìm thấy bài viết');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $video_url = trim($_POST['video_url'] ?? '');
    $shopee_link = trim($_POST['shopee_link'] ?? '');
    $external_url = trim($_POST['external_url'] ?? '');
    $type = $_POST['type'] ?? 'article';
    $status = $_POST['status'] ?? 'draft';
    $meta_title = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_keywords = trim($_POST['meta_keywords'] ?? '');

    if ($title === '') {
        $errors[] = 'Tiêu đề không được bỏ trống';
    }

    $thumbnail = handle_upload('thumbnail') ?: $post['thumbnail'];
    $meta_image = handle_upload('meta_image') ?: $post['meta_image'];

    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE posts SET title=:title, content=:content, thumbnail=:thumbnail, video_url=:video_url, shopee_link=:shopee_link, external_url=:external_url, meta_title=:meta_title, meta_description=:meta_description, meta_keywords=:meta_keywords, meta_image=:meta_image, type=:type, status=:status, updated_at=NOW() WHERE id=:id');
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':thumbnail' => $thumbnail,
            ':video_url' => $video_url,
            ':shopee_link' => $shopee_link,
            ':external_url' => $external_url,
            ':meta_title' => $meta_title,
            ':meta_description' => $meta_description,
            ':meta_keywords' => $meta_keywords,
            ':meta_image' => $meta_image,
            ':type' => $type,
            ':status' => $status,
            ':id' => $id,
        ]);
        redirect_with_message('/admin/posts.php', 'Đã cập nhật bài viết');
    }
}
?>
<h2>Sửa bài viết</h2>
<?php foreach ($errors as $err): ?>
    <div class="alert"><?php echo escape_html($err); ?></div>
<?php endforeach; ?>
<form method="post" enctype="multipart/form-data">
    <label>Tiêu đề</label>
    <input type="text" name="title" value="<?php echo escape_html($post['title']); ?>" required>

    <label>Nội dung</label>
    <textarea name="content" id="editor"><?php echo escape_html($post['content']); ?></textarea>

    <label>Thumbnail hiện tại</label>
    <?php if ($post['thumbnail']): ?><div><img src="<?php echo escape_html($post['thumbnail']); ?>" alt="thumb" style="max-width:150px;"></div><?php endif; ?>
    <input type="file" name="thumbnail" accept="image/*">

    <label>Meta Image hiện tại</label>
    <?php if ($post['meta_image']): ?><div><img src="<?php echo escape_html($post['meta_image']); ?>" alt="meta" style="max-width:150px;"></div><?php endif; ?>
    <input type="file" name="meta_image" accept="image/*">

    <label>Video URL</label>
    <input type="text" name="video_url" value="<?php echo escape_html($post['video_url']); ?>">

    <label>Shopee Link</label>
    <input type="text" name="shopee_link" value="<?php echo escape_html($post['shopee_link']); ?>">

    <label>External URL</label>
    <input type="text" name="external_url" value="<?php echo escape_html($post['external_url']); ?>">

    <label>Loại</label>
    <select name="type">
        <option value="article" <?php if ($post['type']==='article') echo 'selected'; ?>>Bài viết</option>
        <option value="announcement" <?php if ($post['type']==='announcement') echo 'selected'; ?>>Thông báo</option>
        <option value="video" <?php if ($post['type']==='video') echo 'selected'; ?>>Video</option>
    </select>

    <label>Meta title</label>
    <input type="text" name="meta_title" value="<?php echo escape_html($post['meta_title']); ?>">

    <label>Meta description</label>
    <textarea name="meta_description"><?php echo escape_html($post['meta_description']); ?></textarea>

    <label>Meta keywords</label>
    <textarea name="meta_keywords"><?php echo escape_html($post['meta_keywords']); ?></textarea>

    <label>Trạng thái</label>
    <select name="status">
        <option value="draft" <?php if ($post['status']==='draft') echo 'selected'; ?>>Nháp</option>
        <option value="published" <?php if ($post['status']==='published') echo 'selected'; ?>>Xuất bản</option>
    </select>

    <button type="submit">Lưu thay đổi</button>
</form>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('editor');</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
