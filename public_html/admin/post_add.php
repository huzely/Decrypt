<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/posts.php';
require_once __DIR__ . '/../app/lib/helpers.php';

$post = ['title' => '', 'slug' => '', 'excerpt' => '', 'content' => '', 'media_url' => '', 'is_public' => 1, 'published_at' => date('Y-m-d H:i:s')];
if (!empty($_GET['copy'])) {
    $copy = find_post_by_id((int)$_GET['copy']);
    if ($copy) {
        $post = $copy;
        $post['slug'] = unique_slug($copy['slug'] . '-copy');
        $post['title'] .= ' (Copy)';
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $error = 'Token không hợp lệ';
    } else {
        $title = sanitize_text($_POST['title'] ?? '');
        $slug = unique_slug($_POST['slug'] ?: $title);
        $data = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => sanitize_text($_POST['excerpt'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'media_url' => sanitize_text($_POST['media_url'] ?? ''),
            'is_public' => isset($_POST['is_public']) ? 1 : 0,
            'published_at' => sanitize_text($_POST['published_at'] ?? date('Y-m-d H:i:s')),
        ];
        create_post($data);
        admin_log('create_post');
        header('Location: /admin/posts.php');
        exit;
    }
}
include __DIR__ . '/includes/header.php';
?>
<section class="card admin-card">
    <h2>Thêm bài viết</h2>
    <?php if ($error): ?><p class="error"><?php echo e($error); ?></p><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
        <label>Tiêu đề</label>
        <input type="text" name="title" value="<?php echo e($post['title']); ?>" required>
        <label>Slug</label>
        <input type="text" name="slug" value="<?php echo e($post['slug']); ?>" placeholder="bai-viet-demo">
        <label>Mô tả ngắn</label>
        <textarea name="excerpt" required><?php echo e($post['excerpt']); ?></textarea>
        <label>Nội dung</label>
        <textarea name="content" rows="8" required><?php echo e($post['content']); ?></textarea>
        <label>Media URL (Telegram)</label>
        <input type="text" name="media_url" value="<?php echo e($post['media_url']); ?>">
        <label>Ngày xuất bản</label>
        <input type="text" name="published_at" value="<?php echo e($post['published_at']); ?>">
        <label><input type="checkbox" name="is_public" value="1" <?php echo $post['is_public'] ? 'checked' : ''; ?>> Public</label>
        <button class="btn" type="submit">Lưu</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
