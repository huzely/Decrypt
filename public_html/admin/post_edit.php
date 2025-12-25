<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/db.php';
require_once __DIR__ . '/../app/lib/slugify.php';
require_once __DIR__ . '/../app/lib/csrf.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM articles WHERE id=:id');
$stmt->execute([':id'=>$id]);
$article = $stmt->fetch();
if(!$article){exit('Not found');}
if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_verify();
    $slug = $_POST['slug'] ?: slugify($_POST['title']);
    $u = db()->prepare('UPDATE articles SET slug=:slug,title=:title,excerpt=:excerpt,content=:content,telegram_media=:media,meta_title=:mt,meta_description=:md,meta_keywords=:mk,status=:status WHERE id=:id');
    $u->execute([
        ':slug'=>$slug,':title'=>$_POST['title'],':excerpt'=>$_POST['excerpt'],':content'=>$_POST['content'],':media'=>$_POST['telegram_media'],':mt'=>$_POST['meta_title'],':md'=>$_POST['meta_description'],':mk'=>$_POST['meta_keywords'],':status'=>$_POST['status'],':id'=>$id
    ]);
    if ($article['status'] !== 'public' && $_POST['status'] === 'public') {
        require_once __DIR__ . '/../app/lib/telegram.php';
        sendTelegramMessage('Bài mới: '.$_POST['title'].' - '.BASE_URL.'/'.$slug);
    }
    header('Location: '.BASE_URL.'/admin/posts.php');
    exit;
}
include __DIR__ . '/includes/header.php';
?>
<h3>Sửa bài</h3>
<form method="post">
    <?= csrf_field() ?>
    <label>Tiêu đề<input type="text" name="title" value="<?= htmlspecialchars($article['title']) ?>" required></label>
    <label>Slug<input type="text" name="slug" value="<?= htmlspecialchars($article['slug']) ?>"></label>
    <label>Tóm tắt<textarea name="excerpt"><?= htmlspecialchars($article['excerpt']) ?></textarea></label>
    <label>Nội dung<textarea name="content" rows="10"><?= htmlspecialchars($article['content']) ?></textarea></label>
    <label>Telegram media<textarea name="telegram_media"><?= htmlspecialchars($article['telegram_media']) ?></textarea></label>
    <label>Meta title<input type="text" name="meta_title" value="<?= htmlspecialchars($article['meta_title']) ?>"></label>
    <label>Meta description<input type="text" name="meta_description" value="<?= htmlspecialchars($article['meta_description']) ?>"></label>
    <label>Meta keywords<input type="text" name="meta_keywords" value="<?= htmlspecialchars($article['meta_keywords']) ?>"></label>
    <label>Trạng thái<select name="status"><option value="draft" <?= $article['status']==='draft'?'selected':'' ?>>Draft</option><option value="public" <?= $article['status']==='public'?'selected':'' ?>>Public</option></select></label>
    <button class="btn" type="submit">Lưu</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
