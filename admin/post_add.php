<?php
require_once __DIR__ . '/includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $video = trim($_POST['telegram_video_url'] ?? '');
    $image = trim($_POST['telegram_image_url'] ?? '');
    $thumbUpload = handle_upload('thumbnail');
    $metaUpload = handle_upload('meta_image');
    $thumbnail = $thumbUpload ?? trim($_POST['thumbnail_existing'] ?? '');
    $metaImage = $metaUpload ?? trim($_POST['meta_image_existing'] ?? '');
    $shopee = trim($_POST['shopee_link'] ?? '');
    $external = trim($_POST['external_url'] ?? '');
    $type = $_POST['type'] ?? 'article';
    $status = $_POST['status'] ?? 'draft';
    $meta_title = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_keywords = trim($_POST['meta_keywords'] ?? '');

    $stmt = $pdo->prepare('INSERT INTO posts (title, content, thumbnail, telegram_image_url, telegram_video_url, shopee_link, external_url, meta_title, meta_description, meta_keywords, meta_image, type, status, created_at, updated_at) VALUES (:title,:content,:thumbnail,:tg_img,:tg_vid,:shopee,:ext,:mt,:md,:mk,:mimg,:type,:status,NOW(),NOW())');
    $stmt->execute([
        ':title' => $title,
        ':content' => $content,
        ':thumbnail' => $thumbnail,
        ':tg_img' => $image,
        ':tg_vid' => $video,
        ':shopee' => $shopee,
        ':ext' => $external,
        ':mt' => $meta_title,
        ':md' => $meta_description,
        ':mk' => $meta_keywords,
        ':mimg' => $metaImage,
        ':type' => $type,
        ':status' => $status,
    ]);

    if ($status === 'published') {
        notify_telegram('Đã publish bài #' . $pdo->lastInsertId());
    }
    redirect_with_message('/admin/posts.php', 'Đã tạo bài viết');
}

include __DIR__ . '/includes/header.php';
?>
<h1>Thêm bài</h1>
<form method="post" enctype="multipart/form-data">
    <div><label>Tiêu đề</label><br><input type="text" name="title" required></div>
    <div><label>Nội dung</label><br><textarea name="content" rows="5"></textarea></div>
    <div><label>Thumbnail upload</label><br><input type="file" name="thumbnail"></div>
    <div><label>Ảnh Telegram URL</label><br><input type="text" name="telegram_image_url"></div>
    <div><label>Video Telegram URL</label><br><input type="text" name="telegram_video_url"></div>
    <div><label>Meta image upload</label><br><input type="file" name="meta_image"></div>
    <div><label>Link Shopee</label><br><input type="text" name="shopee_link"></div>
    <div><label>Link ngoài</label><br><input type="text" name="external_url"></div>
    <div><label>Loại</label><br>
        <select name="type">
            <option value="article">article</option>
            <option value="announcement">announcement</option>
            <option value="video">video</option>
        </select>
    </div>
    <div><label>Trạng thái</label><br>
        <select name="status">
            <option value="draft">draft</option>
            <option value="published">published</option>
        </select>
    </div>
    <div><label>Meta title</label><br><input type="text" name="meta_title"></div>
    <div><label>Meta description</label><br><textarea name="meta_description"></textarea></div>
    <div><label>Meta keywords</label><br><textarea name="meta_keywords"></textarea></div>
    <button class="button" type="submit">Lưu</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
