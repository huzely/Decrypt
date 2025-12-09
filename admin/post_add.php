<?php
require_once __DIR__ . '/includes/header.php';

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

    $thumbnail = handle_upload('thumbnail');
    $meta_image = handle_upload('meta_image');

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO posts (title, content, thumbnail, video_url, shopee_link, external_url, meta_title, meta_description, meta_keywords, meta_image, type, status, created_at, updated_at) VALUES (:title, :content, :thumbnail, :video_url, :shopee_link, :external_url, :meta_title, :meta_description, :meta_keywords, :meta_image, :type, :status, NOW(), NOW())');
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
        ]);
        redirect_with_message('/admin/posts.php', 'Đã tạo bài viết');
    }
}
?>
<h2>Thêm bài viết</h2>
<?php foreach ($errors as $err): ?>
    <div class="alert"><?php echo escape_html($err); ?></div>
<?php endforeach; ?>
<form method="post" enctype="multipart/form-data">
    <label>Tiêu đề</label>
    <input type="text" name="title" required>

    <label>Nội dung</label>
    <textarea name="content" id="editor"></textarea>

    <label>Thumbnail</label>
    <input type="file" name="thumbnail" accept="image/*">

    <label>Meta Image</label>
    <input type="file" name="meta_image" accept="image/*">

    <label>Video URL</label>
    <input type="text" name="video_url">

    <label>Shopee Link</label>
    <input type="text" name="shopee_link">

    <label>External URL</label>
    <input type="text" name="external_url">

    <label>Loại</label>
    <select name="type">
        <option value="article">Bài viết</option>
        <option value="announcement">Thông báo</option>
        <option value="video">Video</option>
    </select>

    <label>Meta title</label>
    <input type="text" name="meta_title">

    <label>Meta description</label>
    <textarea name="meta_description"></textarea>

    <label>Meta keywords</label>
    <textarea name="meta_keywords"></textarea>

    <label>Trạng thái</label>
    <select name="status">
        <option value="draft">Nháp</option>
        <option value="published">Xuất bản</option>
    </select>

    <button type="submit">Lưu</button>
</form>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('editor');</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
