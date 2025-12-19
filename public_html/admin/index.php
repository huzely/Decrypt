<?php
$config = require __DIR__ . '/../app/config/config.php';
require __DIR__ . '/../app/helpers/util.php';
require __DIR__ . '/../app/models/Article.php';
require __DIR__ . '/../app/models/SiteSettings.php';
require __DIR__ . '/../app/models/Admin.php';
require __DIR__ . '/../app/models/ClickEvent.php';

$pdo = Database::getConnection();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$error = '';
if (isset($_POST['username'], $_POST['password'])) {
    $admin = Admin::findByUsername($_POST['username']);
    if ($admin && password_verify($_POST['password'], $admin['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        Admin::log($admin['id'], 'login');
        header('Location: index.php');
        exit;
    } else {
        $error = 'Sai tài khoản hoặc mật khẩu';
    }
}

if (!is_logged_in()) {
    ?>
    <link rel="stylesheet" href="../assets/css/style.css">
    <div class="container admin-shell">
        <h2>Đăng nhập quản trị</h2>
        <?php if ($error): ?><p style="color:red;"><?php echo e($error); ?></p><?php endif; ?>
        <form method="post">
            <div><input type="text" name="username" placeholder="Username" required></div>
            <div><input type="password" name="password" placeholder="Password" required></div>
            <button class="btn" type="submit">Đăng nhập</button>
        </form>
    </div>
    <?php
    exit;
}

$action = $_GET['action'] ?? 'dashboard';

if ($action === 'save_article' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'body' => $_POST['body'],
        'media_url' => $_POST['media_url'],
        'is_public' => isset($_POST['is_public']) ? 1 : 0,
        'published_at' => $_POST['published_at'] ?: date('Y-m-d H:i:s'),
    ];
    if (!empty($_POST['id'])) {
        Article::update((int)$_POST['id'], $data);
        Admin::log($_SESSION['admin_id'], 'update_article');
    } else {
        Article::create($data);
        Admin::log($_SESSION['admin_id'], 'create_article');
    }
    header('Location: index.php');
    exit;
}

if ($action === 'delete' && isset($_GET['id'])) {
    Article::delete((int)$_GET['id']);
    Admin::log($_SESSION['admin_id'], 'delete_article');
    header('Location: index.php');
    exit;
}

if ($action === 'copy' && isset($_GET['id'])) {
    Article::copy((int)$_GET['id']);
    Admin::log($_SESSION['admin_id'], 'copy_article');
    header('Location: index.php');
    exit;
}

if ($action === 'settings' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $settingsData = [
        'site_name' => $_POST['site_name'],
        'logo_url' => $_POST['logo_url'],
        'banner_url' => $_POST['banner_url'],
        'primary_color' => $_POST['primary_color'],
        'secondary_color' => $_POST['secondary_color'],
        'ad_link' => $_POST['ad_link'],
        'ad_title' => $_POST['ad_title'],
        'ad_body' => $_POST['ad_body'],
    ];
    SiteSettings::set($settingsData);
    Admin::log($_SESSION['admin_id'], 'update_settings');
    header('Location: index.php');
    exit;
}

if ($action === 'reset_clicks') {
    ClickEvent::reset();
    Admin::log($_SESSION['admin_id'], 'reset_clicks');
    header('Location: index.php?reset=1');
    exit;
}

$articles = $pdo->query('SELECT * FROM articles ORDER BY published_at DESC')->fetchAll();
$editArticle = ['id' => '', 'title' => '', 'description' => '', 'body' => '', 'media_url' => '', 'is_public' => 1, 'published_at' => date('Y-m-d H:i:s')];
if ($action === 'edit' && isset($_GET['id'])) {
    $existing = Article::find((int)$_GET['id']);
    if ($existing) {
        $editArticle = $existing;
    }
}
$currentSettings = SiteSettings::get();
$stats = ClickEvent::stats();
?>
<link rel="stylesheet" href="../assets/css/style.css">
<div class="container admin-shell">
    <div class="admin-nav">
        <h2>Quản trị</h2>
        <div>
            <a class="btn secondary" href="index.php?action=dashboard">Dashboard</a>
            <a class="btn secondary" href="index.php?action=settings_view">Cấu hình</a>
            <a class="btn" href="?logout=1">Đăng xuất</a>
        </div>
    </div>

    <?php if ($action === 'settings_view'): ?>
        <h3>Cấu hình giao diện & Shopee</h3>
        <form method="post" action="?action=settings">
            <div><label>Tên website</label><input type="text" name="site_name" value="<?php echo e($currentSettings['site_name'] ?? ''); ?>" required></div>
            <div><label>Logo URL (Telegram)</label><input type="text" name="logo_url" value="<?php echo e($currentSettings['logo_url'] ?? ''); ?>" required></div>
            <div><label>Banner URL (Telegram)</label><input type="text" name="banner_url" value="<?php echo e($currentSettings['banner_url'] ?? ''); ?>" required></div>
            <div><label>Màu chủ đạo</label><input type="color" name="primary_color" value="<?php echo e($currentSettings['primary_color'] ?? '#e63946'); ?>"></div>
            <div><label>Màu phụ</label><input type="color" name="secondary_color" value="<?php echo e($currentSettings['secondary_color'] ?? '#1d3557'); ?>"></div>
            <hr>
            <div><label>Shopee Ad Link</label><input type="url" name="ad_link" value="<?php echo e($currentSettings['ad_link'] ?? ''); ?>"></div>
            <div><label>Ad Title</label><input type="text" name="ad_title" value="<?php echo e($currentSettings['ad_title'] ?? ''); ?>"></div>
            <div><label>Ad Body</label><textarea name="ad_body" rows="3"><?php echo e($currentSettings['ad_body'] ?? ''); ?></textarea></div>
            <button class="btn" type="submit">Lưu</button>
        </form>
    <?php else: ?>
        <h3>Thêm / sửa bài viết</h3>
        <form method="post" action="?action=save_article">
            <input type="hidden" name="id" value="<?php echo e($editArticle['id']); ?>">
            <div><label>Tiêu đề</label><input type="text" name="title" value="<?php echo e($editArticle['title']); ?>" required></div>
            <div><label>Mô tả</label><textarea name="description" rows="2" required><?php echo e($editArticle['description']); ?></textarea></div>
            <div><label>Nội dung</label><textarea name="body" rows="6" required><?php echo e($editArticle['body']); ?></textarea></div>
            <div><label>Link ảnh/video (Telegram)</label><input type="text" name="media_url" value="<?php echo e($editArticle['media_url']); ?>"></div>
            <div><label>Ngày xuất bản</label><input type="text" name="published_at" value="<?php echo e($editArticle['published_at']); ?>"></div>
            <div><label><input type="checkbox" name="is_public" value="1" <?php echo $editArticle['is_public'] ? 'checked' : ''; ?>> Public</label></div>
            <button class="btn" type="submit">Lưu bài viết</button>
        </form>

        <h3>Danh sách bài viết</h3>
        <table class="table">
            <thead><tr><th>ID</th><th>Tiêu đề</th><th>Trạng thái</th><th>Ngày</th><th>Hành động</th></tr></thead>
            <tbody>
            <?php foreach ($articles as $a): ?>
                <tr>
                    <td><?php echo (int)$a['id']; ?></td>
                    <td><?php echo e($a['title']); ?></td>
                    <td><?php echo $a['is_public'] ? 'Public' : 'Ẩn'; ?></td>
                    <td><?php echo e($a['published_at']); ?></td>
                    <td>
                        <a class="btn secondary" href="?action=edit&id=<?php echo (int)$a['id']; ?>">Sửa</a>
                        <a class="btn" href="?action=copy&id=<?php echo (int)$a['id']; ?>">Copy</a>
                        <a class="btn danger" href="?action=delete&id=<?php echo (int)$a['id']; ?>" onclick="return confirm('Xoá?');">Xoá</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Thống kê & chống click ảo</h3>
        <p>Article view: <?php echo $stats['article_view'] ?? 0; ?> | Forced redirect: <?php echo $stats['ad_forced_redirect'] ?? 0; ?> | Close click: <?php echo $stats['ad_close_click'] ?? 0; ?></p>
        <a class="btn danger" href="?action=reset_clicks" onclick="return confirm('Reset số liệu?');">Reset</a>
    <?php endif; ?>
</div>
