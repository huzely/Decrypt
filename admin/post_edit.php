<?php
require_once __DIR__ . '/includes/auth_check.php';
$id = (int)($_GET['id'] ?? 0);
$post = fetch_post($pdo, $id);
if (!$post) {
    redirect_with_message('/admin/posts.php', 'Không tìm thấy bài');
}

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

    $stmt = $pdo->prepare('UPDATE posts SET title=:title, content=:content, thumbnail=:thumbnail, telegram_image_url=:tg_img, telegram_video_url=:tg_vid, shopee_link=:shopee, external_url=:ext, meta_title=:mt, meta_description=:md, meta_keywords=:mk, meta_image=:mimg, type=:type, status=:status, updated_at=NOW() WHERE id=:id');
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
        ':id' => $id,
    ]);

    if ($status === 'published') {
        notify_telegram('Đã publish bài #' . $id);
    }
    redirect_with_message('/admin/posts.php', 'Đã cập nhật bài viết');
}

include __DIR__ . '/includes/header.php';
?>
<h1>Sửa bài</h1>
<form method="post" enctype="multipart/form-data">
    <div><label>Tiêu đề</label><br><input type="text" name="title" value="<?= escape_html($post['title']); ?>" required></div>
    <div><label>Nội dung</label><br><textarea name="content" rows="5"><?= escape_html($post['content']); ?></textarea></div>
    <div><label>Thumbnail upload</label><br><input type="file" name="thumbnail"></div>
    <input type="hidden" name="thumbnail_existing" value="<?= escape_html($post['thumbnail']); ?>">
    <div><label>Ảnh Telegram URL</label><br><input type="text" name="telegram_image_url" value="<?= escape_html($post['telegram_image_url']); ?>"></div>
    <div><label>Video Telegram URL</label><br><input type="text" name="telegram_video_url" value="<?= escape_html($post['telegram_video_url']); ?>"></div>
    <div><label>Meta image upload</label><br><input type="file" name="meta_image"></div>
    <input type="hidden" name="meta_image_existing" value="<?= escape_html($post['meta_image']); ?>">
    <div><label>Link Shopee</label><br><input type="text" name="shopee_link" value="<?= escape_html($post['shopee_link']); ?>"></div>
    <div><label>Link ngoài</label><br><input type="text" name="external_url" value="<?= escape_html($post['external_url']); ?>"></div>
    <div><label>Loại</label><br>
        <select name="type">
            <?php foreach (['article','announcement','video'] as $opt): ?>
                <option value="<?= $opt; ?>" <?= $opt===$post['type']?'selected':''; ?>><?= $opt; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div><label>Trạng thái</label><br>
        <select name="status">
            <?php foreach (['draft','published'] as $opt): ?>
                <option value="<?= $opt; ?>" <?= $opt===$post['status']?'selected':''; ?>><?= $opt; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div><label>Meta title</label><br><input type="text" name="meta_title" value="<?= escape_html($post['meta_title']); ?>"></div>
    <div><label>Meta description</label><br><textarea name="meta_description"><?= escape_html($post['meta_description']); ?></textarea></div>
    <div><label>Meta keywords</label><br><textarea name="meta_keywords"><?= escape_html($post['meta_keywords']); ?></textarea></div>
    <button class="button" type="submit">Lưu</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
