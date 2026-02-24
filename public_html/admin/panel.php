<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/slugify.php';
require_once __DIR__ . '/../lib/track.php';

admin_require();
$pdo = db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        die('CSRF không hợp lệ');
    }

    $action = $_POST['action'] ?? '';
    if ($action === 'create_post') {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $slug = $slug !== '' ? $slug : slugify($title);
        $content = trim($_POST['content'] ?? '');
        $images = array_filter(array_map('trim', explode("\n", $_POST['images'] ?? '')));
        $videos = array_filter(array_map('trim', explode("\n", $_POST['videos'] ?? '')));
        $stmt = $pdo->prepare('INSERT INTO posts (title, slug, content, images_json, videos_json, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$title, $slug, $content, json_encode(array_values($images)), json_encode(array_values($videos))]);
        $message = 'Đã thêm bài';
    } elseif ($action === 'update_post') {
        $postId = (int)($_POST['post_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $slug = $slug !== '' ? $slug : slugify($title);
        $content = trim($_POST['content'] ?? '');
        $images = array_filter(array_map('trim', explode("\n", $_POST['images'] ?? '')));
        $videos = array_filter(array_map('trim', explode("\n", $_POST['videos'] ?? '')));
        $stmt = $pdo->prepare('UPDATE posts SET title=?, slug=?, content=?, images_json=?, videos_json=? WHERE id=?');
        $stmt->execute([$title, $slug, $content, json_encode(array_values($images)), json_encode(array_values($videos)), $postId]);
        $message = 'Đã cập nhật';
    } elseif ($action === 'delete_post') {
        $postId = (int)($_POST['post_id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->execute([$postId]);
        $message = 'Đã xóa';
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

$settings = load_settings($pdo);
$posts = $pdo->query('SELECT * FROM posts ORDER BY created_at DESC')->fetchAll();
$totalPosts = count($posts);
$totalViews = stat_total($pdo, 'view');
$totalClicks = stat_total($pdo, 'ad_click');
$csrf = csrf_token();
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$baseUrl = $scheme . ($_SERVER['HTTP_HOST'] ?? 'domain');
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
    <script>
    function switchTab(id){
        document.querySelectorAll('.tab').forEach(el=>el.classList.add('hidden'));
        document.getElementById(id).classList.remove('hidden');
    }
    function copyLink(link){
        navigator.clipboard.writeText(link).then(()=>{
            alert('Đã copy link');
        });
    }
    </script>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="top-row">
            <h2>Xin chào, <?php echo htmlspecialchars(admin_name(), ENT_QUOTES, 'UTF-8'); ?></h2>
            <a class="logout" href="/admin/logout.php">Đăng xuất</a>
        </div>
        <div class="nav">
            <button onclick="switchTab('tab-post')">Bài viết</button>
            <button onclick="switchTab('tab-settings')">Cài đặt</button>
            <button onclick="switchTab('tab-stats')">Thống kê</button>
        </div>
        <?php if ($message): ?><p class="badge"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>

        <div id="tab-post" class="tab">
            <h3>Thêm bài</h3>
            <form method="post">
                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action" value="create_post">
                <label>Tiêu đề</label>
                <input name="title" required>
                <label>Slug (để trống tự tạo)</label>
                <input name="slug">
                <label>Nội dung</label>
                <textarea name="content" rows="4"></textarea>
                <label>Link ảnh (mỗi dòng một link Telegram)</label>
                <textarea name="images" rows="4"></textarea>
                <label>Link video (mỗi dòng một link Telegram)</label>
                <textarea name="videos" rows="4"></textarea>
                <button type="submit">Thêm bài</button>
            </form>

            <h3>Danh sách bài</h3>
            <?php foreach ($posts as $item): $itemId = (int)$item['id']; $slugVal = $item['slug']; $full = $baseUrl . '/' . $slugVal; ?>
                <div class="card inner">
                    <div class="row">
                        <strong><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span class="badge">ID <?php echo $itemId; ?></span>
                    </div>
                    <p class="link-copy">Link: <?php echo htmlspecialchars($full, ENT_QUOTES, 'UTF-8'); ?></p>
                    <button type="button" onclick="copyLink('<?php echo htmlspecialchars($full, ENT_QUOTES, 'UTF-8'); ?>')">Copy link</button>
                    <details>
                        <summary>Sửa / Xóa</summary>
                        <form method="post">
                            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="action" value="update_post">
                            <input type="hidden" name="post_id" value="<?php echo $itemId; ?>">
                            <label>Tiêu đề</label>
                            <input name="title" value="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            <label>Slug</label>
                            <input name="slug" value="<?php echo htmlspecialchars($item['slug'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            <label>Nội dung</label>
                            <textarea name="content" rows="4"><?php echo htmlspecialchars($item['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <label>Link ảnh</label>
                            <textarea name="images" rows="3"><?php echo htmlspecialchars(implode("\n", json_decode($item['images_json'], true) ?: []), ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <label>Link video</label>
                            <textarea name="videos" rows="3"><?php echo htmlspecialchars(implode("\n", json_decode($item['videos_json'], true) ?: []), ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <button type="submit">Lưu</button>
                        </form>
                        <form method="post" onsubmit="return confirm('Xóa bài này?');">
                            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="action" value="delete_post">
                            <input type="hidden" name="post_id" value="<?php echo $itemId; ?>">
                            <button type="submit" class="danger">Xóa</button>
                        </form>
                    </details>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="tab-settings" class="tab hidden">
            <h3>Cài đặt</h3>
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
                <label>Link liên hệ admin</label>
                <input name="contact_link" value="<?php echo htmlspecialchars($settings['contact_link'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Facebook hoặc Telegram">
                <button type="submit">Lưu</button>
            </form>
        </div>

        <div id="tab-stats" class="tab hidden">
            <h3>Thống kê</h3>
            <p>Tổng số bài báo: <strong><?php echo $totalPosts; ?></strong></p>
            <p>Tổng lượt xem bài: <strong><?php echo $totalViews; ?></strong></p>
            <p>Tổng lượt click quảng cáo: <strong><?php echo $totalClicks; ?></strong></p>
        </div>
    </div>
</div>
</body>
</html>
