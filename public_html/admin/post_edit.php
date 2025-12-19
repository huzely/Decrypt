<?php
require __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../app/lib/slugify.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM articles WHERE id=? LIMIT 1');
$stmt->execute([$id]);
$article = $stmt->fetch();
if (!$article) { echo 'Not found'; require __DIR__ . '/includes/footer.php'; exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'excerpt' => trim($_POST['excerpt'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'slug' => trim($_POST['slug'] ?? '') ?: slugify($_POST['title'] ?? ''),
        'meta_title' => trim($_POST['meta_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'meta_keywords' => trim($_POST['meta_keywords'] ?? ''),
        'telegram_media' => $_POST['telegram_media'] ?? '',
        'status' => $_POST['status'] ?? 'draft',
    ];
    $mediaJson = json_encode(array_values(array_filter(array_map(function($line){
        $line = trim($line);
        if (!$line) return null;
        $type = (strpos($line, '.mp4') !== false || strpos($line, 'video') !== false) ? 'video' : 'image';
        return ['type'=>$type, 'url'=>$line];
    }, explode("\n", $data['telegram_media'])))));
    try {
        $stmt = $pdo->prepare('UPDATE articles SET slug=?, title=?, excerpt=?, content=?, meta_title=?, meta_description=?, meta_keywords=?, telegram_media=?, status=?, updated_at=NOW() WHERE id=?');
        $stmt->execute([$data['slug'],$data['title'],$data['excerpt'],$data['content'],$data['meta_title'],$data['meta_description'],$data['meta_keywords'],$mediaJson,$data['status'],$id]);
        cache_clear($config);
        header('Location: /admin/posts.php');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<h1>Sửa bài #<?= $id; ?></h1>
<?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES); ?></p><?php endif; ?>
<form method="post">
    <div class="form-group"><label>Tiêu đề</label><input class="form-control" name="title" value="<?= htmlspecialchars($article['title'], ENT_QUOTES); ?>"></div>
    <div class="form-group"><label>Slug</label><input class="form-control" name="slug" value="<?= htmlspecialchars($article['slug'], ENT_QUOTES); ?>"></div>
    <div class="form-group"><label>Excerpt</label><textarea class="form-control" name="excerpt" rows="2"><?= htmlspecialchars($article['excerpt'], ENT_QUOTES); ?></textarea></div>
    <div class="form-group"><label>Nội dung</label><textarea class="form-control" name="content" rows="8"><?= htmlspecialchars($article['content'], ENT_QUOTES); ?></textarea></div>
    <div class="form-group"><label>Telegram media URLs</label><textarea class="form-control" name="telegram_media" rows="4"><?= htmlspecialchars(implode("\n", array_map(function($m){ return $m['url']; }, json_decode($article['telegram_media'], true) ?: [])), ENT_QUOTES); ?></textarea></div>
    <div class="form-group"><label>Meta title</label><input class="form-control" name="meta_title" value="<?= htmlspecialchars($article['meta_title'], ENT_QUOTES); ?>"></div>
    <div class="form-group"><label>Meta description</label><textarea class="form-control" name="meta_description" rows="2"><?= htmlspecialchars($article['meta_description'], ENT_QUOTES); ?></textarea></div>
    <div class="form-group"><label>Meta keywords</label><input class="form-control" name="meta_keywords" value="<?= htmlspecialchars($article['meta_keywords'], ENT_QUOTES); ?>"></div>
    <div class="form-group"><label>Trạng thái</label>
        <select class="form-control" name="status">
            <option value="draft" <?= $article['status']==='draft'?'selected':''; ?>>Draft</option>
            <option value="published" <?= $article['status']==='published'?'selected':''; ?>>Published</option>
        </select>
    </div>
    <button class="btn btn-primary" type="submit">Cập nhật</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
