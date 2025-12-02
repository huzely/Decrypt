<?php
require_once __DIR__ . '/../helpers.php';
require_login();

$settings = fetch_settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['site_title','hero_title','hero_subtitle','primary_color','accent_color','admin_note'];
    foreach ($fields as $field) {
        set_setting($field, trim($_POST[$field] ?? ''));
    }
    header('Location: /admin/settings.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tuỳ chỉnh giao diện</title>
    <link rel="stylesheet" href="/static/css/style.css">
    <style>
        :root { --main: <?php echo sanitize($settings['primary_color']); ?>; }
        body { font-family:'Inter',sans-serif; background:#0f172a; color:#e5e7eb; margin:0; }
        .nav { background:#0b1220; padding:1rem 1.25rem; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #111827; }
        .nav a { color:#e5e7eb; text-decoration:none; font-weight:700; margin-right:1rem; }
        .container { max-width:800px; margin:1.25rem auto; padding:0 1.25rem; }
        input, textarea { width:100%; padding:0.75rem 0.9rem; border-radius:10px; border:1px solid #1f2937; background:#0b1220; color:#e5e7eb; margin-bottom:0.75rem; }
        label { font-weight:700; display:block; margin-bottom:0.2rem; }
        button { padding:0.75rem 1.1rem; border:none; border-radius:10px; background: var(--main); color:#fff; font-weight:700; cursor:pointer; }
        .grid { display:grid; gap:1rem; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); }
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
    <h2>Tuỳ chỉnh giao diện</h2>
    <form method="post">
        <label>Tiêu đề site</label>
        <input type="text" name="site_title" value="<?php echo sanitize($settings['site_title']); ?>">

        <label>Hero title</label>
        <input type="text" name="hero_title" value="<?php echo sanitize($settings['hero_title']); ?>">

        <label>Hero subtitle</label>
        <textarea name="hero_subtitle" rows="2"><?php echo sanitize($settings['hero_subtitle']); ?></textarea>

        <div class="grid">
            <div>
                <label>Màu chính</label>
                <input type="color" name="primary_color" value="<?php echo sanitize($settings['primary_color']); ?>">
            </div>
            <div>
                <label>Màu nhấn</label>
                <input type="color" name="accent_color" value="<?php echo sanitize($settings['accent_color']); ?>">
            </div>
        </div>

        <label>Ghi chú admin</label>
        <textarea name="admin_note" rows="3" placeholder="Nhắc nhở, hướng dẫn giao diện..."><?php echo sanitize($settings['admin_note']); ?></textarea>

        <button type="submit">Lưu thay đổi</button>
    </form>
</div>
</body>
</html>
