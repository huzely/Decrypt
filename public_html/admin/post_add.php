<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/db.php';
require_once __DIR__ . '/../app/lib/slugify.php';
require_once __DIR__ . '/../app/lib/csrf.php';
require_once __DIR__ . '/../app/lib/telegram.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $slug = $_POST['slug'] ?: slugify($_POST['title']);
    $stmt = db()->prepare('INSERT INTO articles (slug,title,excerpt,content,telegram_media,meta_title,meta_description,meta_keywords,status) VALUES (:slug,:title,:excerpt,:content,:media,:mt,:md,:mk,:status)');
    $stmt->execute([
        ':slug'=>$slug,
        ':title'=>$_POST['title'],
        ':excerpt'=>$_POST['excerpt'],
        ':content'=>$_POST['content'],
        ':media'=>$_POST['telegram_media'],
        ':mt'=>$_POST['meta_title'],
        ':md'=>$_POST['meta_description'],
        ':mk'=>$_POST['meta_keywords'],
        ':status'=>$_POST['status']
    ]);
    if ($_POST['status']==='public') {
        sendTelegramMessage('Bài mới: '.$_POST['title'].' - '.BASE_URL.'/'.$slug);
    }
    header('Location: '.BASE_URL.'/admin/posts.php');
    exit;
}
include __DIR__ . '/includes/header.php';
?>
<h3>Thêm bài</h3>
<form method="post">
    <?= csrf_field() ?>
    <label>Tiêu đề<input type="text" name="title" required></label>
    <label>Slug<input type="text" name="slug"></label>
    <label>Tóm tắt<textarea name="excerpt"></textarea></label>
    <label>Nội dung<textarea name="content" rows="10"></textarea></label>
    <label>Telegram media (mỗi dòng một link)<textarea name="telegram_media"></textarea></label>
    <label>Meta title<input type="text" name="meta_title"></label>
    <label>Meta description<input type="text" name="meta_description"></label>
    <label>Meta keywords<input type="text" name="meta_keywords"></label>
    <label>Trạng thái<select name="status"><option value="draft">Draft</option><option value="public">Public</option></select></label>
    <button class="btn" type="submit">Lưu</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
