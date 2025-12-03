<?php
require_once __DIR__ . '/../bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'reset_all') {
        reset_all_clicks($pdo);
        $_SESSION['flash'] = 'Đã reset toàn bộ lượt click';
        header('Location: /admin/dashboard.php');
        exit;
    }
    if ($action === 'reset_one') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            reset_link_clicks($pdo, $id);
        }
        $_SESSION['flash'] = 'Đã reset lượt click';
        header('Location: /admin/dashboard.php');
        exit;
    }

    [$ok, $msg, $newId] = save_link($pdo, $_POST) + [2 => null];
    if ($ok) {
        $_SESSION['flash'] = $msg;
        if (isset($newId)) {
            send_telegram($config, '🔗 Tạo link mới: ' . base_url() . '/' . htmlspecialchars($_POST['slug'] ?? ''));
        }
        header('Location: /admin/dashboard.php');
        exit;
    }
    $_SESSION['flash'] = $msg;
    header('Location: ' . ($_POST['id'] ? '/admin/link_edit.php?id=' . (int)$_POST['id'] : '/admin/link_edit.php'));
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$link = $id ? fetch_link($pdo, $id) : null;
$branding = get_branding($pdo);
?><!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $id ? 'Sửa link' : 'Tạo link mới'; ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
    <style><?php echo render_styles($branding); ?></style>
</head>
<body>
<header>
    <h1><?php echo $id ? 'Sửa link' : 'Tạo link mới'; ?></h1>
    <p class="subtitle"><a href="/admin/dashboard.php">← Quay lại bảng điều khiển</a></p>
</header>
<div class="container">
    <div class="card">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($link['id'] ?? ''); ?>">
            <label>Slug</label>
            <input type="text" name="slug" required value="<?php echo htmlspecialchars($link['slug'] ?? ''); ?>" placeholder="vd: magiamgia">
            <label>Link Shopee</label>
            <input type="url" name="shopee_url" required value="<?php echo htmlspecialchars($link['shopee_url'] ?? ''); ?>">
            <label>Link Telegram</label>
            <input type="url" name="telegram_url" required value="<?php echo htmlspecialchars($link['telegram_url'] ?? ''); ?>">
            <label>Meta title</label>
            <input type="text" name="meta_title" value="<?php echo htmlspecialchars($link['meta_title'] ?? ''); ?>">
            <label>Meta description</label>
            <textarea name="meta_description"><?php echo htmlspecialchars($link['meta_description'] ?? ''); ?></textarea>
            <label>Ảnh meta (fake preview)</label>
            <input type="file" name="meta_image" accept="image/*">
            <?php if (!empty($link['meta_image'])): ?>
                <p class="subtitle">Ảnh hiện tại: <a href="/<?php echo htmlspecialchars($link['meta_image']); ?>" target="_blank">Xem</a></p>
                <input type="hidden" name="meta_image_old" value="<?php echo htmlspecialchars($link['meta_image']); ?>">
            <?php endif; ?>
            <button class="btn" type="submit">Lưu</button>
        </form>
    </div>
</div>
</body>
</html>
