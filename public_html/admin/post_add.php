<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/slugify.php';
require_once __DIR__ . '/../lib/telegram.php';
require_auth();

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
        $stmt = $pdo->prepare("INSERT INTO articles(title, slug, excerpt, content, telegram_media, meta_title, meta_description, meta_keywords, status, created_at, updated_at) VALUES(:title,:slug,:excerpt,:content,:media,:mt,:md,:mk,:status,NOW(),NOW())");
        try {
            $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':excerpt' => $excerpt,
                ':content' => $content,
                ':media' => $telegram_media,
                ':mt' => $meta_title,
                ':md' => $meta_description,
                ':mk' => $meta_keywords,
                ':status' => $status,
            ]);
            queueTelegramMessage('Bài mới: ' . $title . ' - ' . current_url($slug));
            redirect('/admin/posts.php');
        } catch (Throwable $e) {
            $message = 'Không thể lưu bài: ' . $e->getMessage();
        }
    }
}
include __DIR__ . '/includes/header.php';
?>
<div class="topbar"><h1>Thêm bài</h1></div>
<?php if ($message): ?><div class="alert"><?php echo e($message); ?></div><?php endif; ?>
<form method="POST">
    <label>Tiêu đề</label>
    <input name="title" required value="<?php echo e($_POST['title'] ?? ''); ?>">
    <label>Slug</label>
    <input name="slug" value="<?php echo e($_POST['slug'] ?? ''); ?>" placeholder="tieu-de-khong-dau">
    <label>Tóm tắt</label>
    <textarea name="excerpt" rows="2"><?php echo e($_POST['excerpt'] ?? ''); ?></textarea>
    <label>Nội dung</label>
    <textarea name="content" rows="8" required><?php echo e($_POST['content'] ?? ''); ?></textarea>
    <label>Telegram media (mỗi dòng 1 link)</label>
    <textarea name="telegram_media" rows="3"><?php echo e($_POST['telegram_media'] ?? ''); ?></textarea>
    <label>Meta title</label>
    <input name="meta_title" value="<?php echo e($_POST['meta_title'] ?? ''); ?>">
    <label>Meta description</label>
    <input name="meta_description" value="<?php echo e($_POST['meta_description'] ?? ''); ?>">
    <label>Meta keywords</label>
    <input name="meta_keywords" value="<?php echo e($_POST['meta_keywords'] ?? ''); ?>">
    <label>Trạng thái</label>
    <select name="status">
        <option value="draft">Nháp</option>
        <option value="public">Public</option>
    </select>
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <button class="btn" type="submit">Lưu</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
