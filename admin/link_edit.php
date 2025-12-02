<?php
require_once __DIR__ . '/../helpers.php';
require_login();

$pdo = get_pdo();
$settings = fetch_settings();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$link = null;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM links WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $link = $stmt->fetch();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = ensure_slug($_POST['slug'] ?? '', $title);
    $shopee = trim($_POST['shopee_url'] ?? '');
    $telegram = trim($_POST['telegram_url'] ?? '');
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDesc = trim($_POST['meta_description'] ?? '');
    $contentHtml = trim($_POST['content_html'] ?? '');

    if (!$title || !$shopee || !$telegram) {
        $errors[] = 'Tiêu đề, Shopee URL và Telegram URL là bắt buộc';
    }

    $checkSlug = $pdo->prepare('SELECT id FROM links WHERE slug = ? AND id != ?');
    $checkSlug->execute([$slug, $id ?: 0]);
    if ($checkSlug->fetch()) {
        $errors[] = 'Slug đã tồn tại, hãy chọn slug khác';
    }

    $metaImageName = $link['meta_image'] ?? null;
    if (!empty($_FILES['meta_image']['name'])) {
        $file = $_FILES['meta_image'];
        if (strpos($file['type'], 'image/') !== 0) {
            $errors[] = 'Tệp meta phải là ảnh';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $metaImageName = uniqid('meta_', true) . '.' . $ext;
            move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $metaImageName);
        }
    }

    if (empty($errors)) {
        if ($id && $link) {
            $stmt = $pdo->prepare('UPDATE links SET title=?, slug=?, shopee_url=?, telegram_url=?, meta_title=?, meta_description=?, meta_image=?, content_html=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$title, $slug, $shopee, $telegram, $metaTitle, $metaDesc, $metaImageName, $contentHtml, $id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO links (title, slug, shopee_url, telegram_url, meta_title, meta_description, meta_image, content_html, created_by) VALUES (?,?,?,?,?,?,?,?,?)');
            $stmt->execute([$title, $slug, $shopee, $telegram, $metaTitle, $metaDesc, $metaImageName, $contentHtml, current_user()['id']]);
            send_telegram_message("Link mới: {$title}\nSlug: {$slug}");
        }
        header('Location: /admin/links.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Sửa link' : 'Tạo link'; ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
    <style>
        :root { --main: <?php echo sanitize($settings['primary_color']); ?>; }
        body { font-family:'Inter',sans-serif; background:#0f172a; color:#e5e7eb; margin:0; }
        .nav { background:#0b1220; padding:1rem 1.25rem; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #111827; }
        .nav a { color:#e5e7eb; text-decoration:none; font-weight:700; margin-right:1rem; }
        .container { max-width:900px; margin:1.25rem auto; padding:0 1.25rem; }
        input, textarea { width:100%; padding:0.75rem 0.9rem; border-radius:10px; border:1px solid #1f2937; background:#0b1220; color:#e5e7eb; margin-bottom:0.75rem; }
        label { font-weight:700; display:block; margin-bottom:0.2rem; }
        button { padding:0.75rem 1.1rem; border:none; border-radius:10px; background: var(--main); color:#fff; font-weight:700; cursor:pointer; }
        .error { background: rgba(248,113,113,.18); color:#fecdd3; padding:0.65rem 0.75rem; border-radius:10px; margin-bottom:0.75rem; }
        @media (max-width: 780px) {
            .nav { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
            .container { padding: 0 1rem; }
        }
    </style>
</head>
<body>
<div class="nav">
    <div>
        <a href="/admin/dashboard.php">Dashboard</a>
        <a href="/admin/links.php">Link</a>
        <a href="/admin/settings.php">Giao diện</a>
    </div>
    <div>
        <a href="/admin/logout.php">Đăng xuất</a>
    </div>
</div>
<div class="container">
    <h2><?php echo $id ? 'Sửa link' : 'Tạo link mới'; ?></h2>
    <?php foreach ($errors as $err): ?><div class="error"><?php echo sanitize($err); ?></div><?php endforeach; ?>
    <form method="post" enctype="multipart/form-data">
        <label>Tiêu đề</label>
        <input type="text" name="title" value="<?php echo sanitize($link['title'] ?? ''); ?>" required>

        <label>Slug</label>
        <input type="text" name="slug" value="<?php echo sanitize($link['slug'] ?? ''); ?>" placeholder="ví dụ: my-link">

        <label>Shopee URL</label>
        <input type="url" name="shopee_url" value="<?php echo sanitize($link['shopee_url'] ?? ''); ?>" required>

        <label>Telegram URL</label>
        <input type="url" name="telegram_url" value="<?php echo sanitize($link['telegram_url'] ?? ''); ?>" required>

        <label>Meta title</label>
        <input type="text" name="meta_title" value="<?php echo sanitize($link['meta_title'] ?? ''); ?>">

        <label>Meta description</label>
        <textarea name="meta_description" rows="3"><?php echo sanitize($link['meta_description'] ?? ''); ?></textarea>

        <label>Nội dung hiển thị (HTML nhẹ)</label>
        <textarea name="content_html" rows="4" placeholder="Mô tả ngắn, CTA..."><?php echo isset($link['content_html']) ? sanitize($link['content_html']) : ''; ?></textarea>

        <label>Ảnh meta</label>
        <?php if (!empty($link['meta_image'])): ?>
            <p>Ảnh hiện tại: <a href="<?php echo sanitize(link_meta_image_path($link['meta_image'])); ?>" target="_blank">Xem</a></p>
        <?php endif; ?>
        <input type="file" name="meta_image" accept="image/*">

        <button type="submit">Lưu</button>
    </form>
</div>
</body>
</html>
