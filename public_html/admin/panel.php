<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/slugify.php';
require_once __DIR__ . '/../lib/track.php';

require_admin();
$pdo = get_pdo();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf'] ?? '')) {
        die('CSRF token không hợp lệ');
    }
    $action = $_POST['action'] ?? '';
    if ($action === 'create_post') {
        $title = trim($_POST['title'] ?? '');
        $slugInput = trim($_POST['slug'] ?? '');
        $slug = $slugInput ?: slugify($title);
        $content = trim($_POST['content'] ?? '');
        $images = array_filter(array_map('trim', explode("\n", $_POST['images'] ?? '')));
        $videos = array_filter(array_map('trim', explode("\n", $_POST['videos'] ?? '')));

        $stmt = $pdo->prepare('INSERT INTO posts (title, slug, content, images_json, videos_json, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$title, $slug, $content, json_encode(array_values($images)), json_encode(array_values($videos))]);
        $message = 'Đã thêm bài viết';
    } elseif ($action === 'update_post') {
        $postId = (int)($_POST['post_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $slug = $slug ?: slugify($title);
        $content = trim($_POST['content'] ?? '');
        $images = array_filter(array_map('trim', explode("\n", $_POST['images'] ?? '')));
        $videos = array_filter(array_map('trim', explode("\n", $_POST['videos'] ?? '')));
        $stmt = $pdo->prepare('UPDATE posts SET title=?, slug=?, content=?, images_json=?, videos_json=? WHERE id=?');
        $stmt->execute([$title, $slug, $content, json_encode(array_values($images)), json_encode(array_values($videos)), $postId]);
        $message = 'Đã cập nhật bài viết';
    } elseif ($action === 'delete_post') {
        $postId = (int)($_POST['post_id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->execute([$postId]);
        $message = 'Đã xóa bài viết';
    } elseif ($action === 'save_settings') {
        $adsEnabled = isset($_POST['ads_enabled']) ? 1 : 0;
        $adLink = trim($_POST['ad_link'] ?? '');
        $adTitle = trim($_POST['ad_title'] ?? '');
        $adBody = trim($_POST['ad_body'] ?? '');
        $contactLink = trim($_POST['contact_link'] ?? '');
        $stmt = $pdo->prepare('UPDATE settings SET ads_enabled=?, ad_link=?, ad_title=?, ad_body=?, contact_link=? WHERE id = 1');
        $stmt->execute([$adsEnabled, $adLink, $adTitle, $adBody, $contactLink]);
        $message = 'Đã lưu cài đặt';
    }
}

$settings = fetch_settings($pdo);
$posts = $pdo->query('SELECT * FROM posts ORDER BY created_at DESC')->fetchAll();
$totalPosts = count($posts);
$totalViews = count_stats($pdo, 'view');
$totalClicks = count_stats($pdo, 'ad_click');
$csrf = csrf_token();
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
    <script>
        function showTab(id) {
            document.querySelectorAll('.tab').forEach(el => el.classList.add('hidden'));
            document.getElementById(id).classList.remove('hidden');
        }
    </script>
</head>
<body>
<div class="container">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <h2>Chào, <?php echo htmlspecialchars(current_admin_username(), ENT_QUOTES, 'UTF-8'); ?></h2>
            <a href="/admin/logout.php" style="color:#93c5fd;">Đăng xuất</a>
        </div>
        <div class="nav">
            <button onclick="showTab('tab-posts')">Bài viết</button>
            <button onclick="showTab('tab-settings')">Cài đặt</button>
            <button onclick="showTab('tab-stats')">Thống kê</button>
        </div>
        <?php if ($message): ?><p class="badge"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>

        <div id="tab-posts" class="tab">
            <h3>Thêm bài viết</h3>
            <form method="post">
                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action" value="create_post">
                <label>Tiêu đề</label>
                <input name="title" required>
                <label>Slug (để trống sẽ tự tạo)</label>
                <input name="slug">
                <label>Nội dung</label>
                <textarea name="content" rows="4"></textarea>
                <label>Danh sách ảnh (mỗi dòng 1 link)</label>
                <textarea name="images" rows="4"></textarea>
                <label>Danh sách video (mỗi dòng 1 link)</label>
                <textarea name="videos" rows="4"></textarea>
                <button type="submit">Thêm</button>
            </form>

            <h3>Danh sách bài viết</h3>
            <?php foreach ($posts as $post): ?>
                <div class="card" style="background:#0b1222;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <strong><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span class="badge">ID <?php echo (int)$post['id']; ?></span>
                    </div>
                    <div class="link-copy">Link: <?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'domain', ENT_QUOTES, 'UTF-8'); ?>/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <details>
                        <summary>Sửa/Xóa</summary>
                        <form method="post" style="margin-top:10px;">
                            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="action" value="update_post">
                            <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
                            <label>Tiêu đề</label>
                            <input name="title" value="<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            <label>Slug</label>
                            <input name="slug" value="<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            <label>Nội dung</label>
                            <textarea name="content" rows="4"><?php echo htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <label>Danh sách ảnh</label>
                            <textarea name="images" rows="4"><?php echo htmlspecialchars(implode("\n", json_decode($post['images_json'], true) ?: []), ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <label>Danh sách video</label>
                            <textarea name="videos" rows="4"><?php echo htmlspecialchars(implode("\n", json_decode($post['videos_json'], true) ?: []), ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <button type="submit">Lưu</button>
                        </form>
                        <form method="post" onsubmit="return confirm('Xóa bài viết này?');">
                            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="action" value="delete_post">
                            <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
                            <button type="submit" style="background:#ef4444;">Xóa</button>
                        </form>
                    </details>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="tab-settings" class="tab hidden">
            <h3>Cài đặt quảng cáo & liên hệ</h3>
            <form method="post">
                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action" value="save_settings">
                <label><input type="checkbox" name="ads_enabled" <?php echo !empty($settings['ads_enabled']) ? 'checked' : ''; ?>> Bật quảng cáo</label>
                <label>Link Shopee</label>
                <input name="ad_link" value="<?php echo htmlspecialchars($settings['ad_link'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://s.shopee.vn/xxxx">
                <label>Tiêu đề quảng cáo</label>
                <input name="ad_title" value="<?php echo htmlspecialchars($settings['ad_title'], ENT_QUOTES, 'UTF-8'); ?>">
                <label>Nội dung quảng cáo</label>
                <textarea name="ad_body" rows="3"><?php echo htmlspecialchars($settings['ad_body'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <label>Link Liên hệ admin</label>
                <input name="contact_link" value="<?php echo htmlspecialchars($settings['contact_link'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Facebook hoặc Telegram">
                <button type="submit">Lưu</button>
            </form>
        </div>

        <div id="tab-stats" class="tab hidden">
            <h3>Thống kê</h3>
            <p>Tổng số bài viết: <strong><?php echo $totalPosts; ?></strong></p>
            <p>Tổng lượt xem: <strong><?php echo $totalViews; ?></strong></p>
            <p>Tổng click quảng cáo: <strong><?php echo $totalClicks; ?></strong></p>
        </div>
    </div>
</div>
</body>
</html>
