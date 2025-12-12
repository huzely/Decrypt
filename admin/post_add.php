<?php include __DIR__ . '/includes/header.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $slugInput = $_POST['slug'] ?? slugify($title);
    $slug = ensure_unique_slug($pdo, $slugInput);
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

    $stmt = $pdo->prepare('INSERT INTO posts (slug,title,content,thumbnail,telegram_image_url,telegram_video_url,shopee_link,external_url,meta_title,meta_description,meta_keywords,meta_image,type,status,shopee_click_count,created_at,updated_at) VALUES (:slug,:title,:content,:thumbnail,:ti,:tv,:shopee,:ext,:mt,:md,:mk,:mi,:type,:status,0,NOW(),NOW())');
    $stmt->execute([
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
    ]);
    if ($status === 'published') {
        $id = $pdo->lastInsertId();
        publish_post_notify(['id'=>$id,'title'=>$title,'slug'=>$slug]);
    }
    redirect_with_message('/admin/posts.php','Đã thêm');
}
?>
<h1>Thêm bài</h1>
<form method="post">
    <div class="form-group"><label>Tiêu đề</label><input name="title" required></div>
    <div class="form-group"><label>Slug</label><input name="slug"></div>
    <div class="form-group"><label>Loại</label><select name="type"><option value="article">Article</option><option value="announcement">Announcement</option><option value="video">Video</option></select></div>
    <div class="form-group"><label>Trạng thái</label><select name="status"><option value="draft">Draft</option><option value="published">Published</option></select></div>
    <div class="form-group"><label>Thumbnail URL</label><input name="thumbnail"></div>
    <div class="form-group"><label>Meta Image</label><input name="meta_image"></div>
    <div class="form-group"><label>Telegram image URL</label><input name="telegram_image_url"></div>
    <div class="form-group"><label>Telegram video URL</label><input name="telegram_video_url"></div>
    <div class="form-group"><label>Shopee link</label><input name="shopee_link"></div>
    <div class="form-group"><label>External URL</label><input name="external_url"></div>
    <div class="form-group"><label>Meta title</label><input name="meta_title"></div>
    <div class="form-group"><label>Meta description</label><textarea name="meta_description"></textarea></div>
    <div class="form-group"><label>Meta keywords</label><input name="meta_keywords"></div>
    <div class="form-group"><label>Nội dung</label><textarea name="content" rows="8"></textarea></div>
    <button class="btn primary" type="submit">Lưu</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
