<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/slugify.php';
require_once __DIR__ . '/../lib/telegram.php';
require_auth();
$pageTitle = 'Sửa bài';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM articles WHERE id = :id');
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();
if (!$post) { echo 'Không tìm thấy'; exit; }

$message = '';
if (is_post()) {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $message = 'Token không hợp lệ';
    } else {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $telegram_media = trim($_POST['telegram_media'] ?? '');
        $meta_title = trim($_POST['meta_title'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        $meta_keywords = trim($_POST['meta_keywords'] ?? '');
        $status = $_POST['status'] === 'public' ? 'public' : 'draft';
        $stmtUpdate = $pdo->prepare("UPDATE articles SET title=:title, slug=:slug, excerpt=:excerpt, content=:content, telegram_media=:media, meta_title=:mt, meta_description=:md, meta_keywords=:mk, status=:status, updated_at=NOW() WHERE id=:id");
        try {
            $stmtUpdate->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':excerpt' => $excerpt,
                ':content' => $content,
                ':media' => $telegram_media,
                ':mt' => $meta_title,
                ':md' => $meta_description,
                ':mk' => $meta_keywords,
                ':status' => $status,
                ':id' => $id,
            ]);
            redirect('/admin/posts.php');
        } catch (Throwable $e) {
            $message = 'Không thể cập nhật: ' . $e->getMessage();
        }
    }
}
include __DIR__ . '/includes/header.php';
?>
<div class="topbar"><h1>Sửa bài</h1></div>
<?php if ($message): ?><div class="alert"><?php echo e($message); ?></div><?php endif; ?>
<form method="POST">
    <label>Tiêu đề</label>
    <input name="title" required value="<?php echo e($_POST['title'] ?? $post['title']); ?>">
    <label>Slug</label>
    <input name="slug" value="<?php echo e($_POST['slug'] ?? $post['slug']); ?>">
    <label>Tóm tắt</label>
    <textarea name="excerpt" rows="2"><?php echo e($_POST['excerpt'] ?? $post['excerpt']); ?></textarea>
    <label>Nội dung</label>
    <textarea name="content" rows="8" required><?php echo e($_POST['content'] ?? $post['content']); ?></textarea>
    <label>Telegram media</label>
    <textarea name="telegram_media" rows="3"><?php echo e($_POST['telegram_media'] ?? $post['telegram_media']); ?></textarea>
    <label>Meta title</label>
    <input name="meta_title" value="<?php echo e($_POST['meta_title'] ?? $post['meta_title']); ?>">
    <label>Meta description</label>
    <input name="meta_description" value="<?php echo e($_POST['meta_description'] ?? $post['meta_description']); ?>">
    <label>Meta keywords</label>
    <input name="meta_keywords" value="<?php echo e($_POST['meta_keywords'] ?? $post['meta_keywords']); ?>">
    <label>Trạng thái</label>
    <select name="status">
        <option value="draft" <?php echo (($post['status'] ?? '') === 'draft') ? 'selected' : ''; ?>>Nháp</option>
        <option value="public" <?php echo (($post['status'] ?? '') === 'public') ? 'selected' : ''; ?>>Public</option>
    </select>
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <button class="btn" type="submit">Lưu thay đổi</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
