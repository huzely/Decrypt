<?php
require_once __DIR__ . '/../helpers.php';
require_login();

$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $del = $pdo->prepare('DELETE FROM links WHERE id = ?');
    $del->execute([(int)$_POST['delete_id']]);
    header('Location: /admin/links.php');
    exit;
}

$links = $pdo->query('SELECT l.*, COUNT(c.id) as clicks FROM links l LEFT JOIN clicks c ON c.link_id = l.id GROUP BY l.id ORDER BY l.created_at DESC')->fetchAll();
$settings = fetch_settings();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lí link</title>
    <link rel="stylesheet" href="/static/css/style.css">
    <style>
        :root { --main: <?php echo sanitize($settings['primary_color']); ?>; }
        body { font-family:'Inter',sans-serif; background:#0f172a; color:#e5e7eb; margin:0; }
        .nav { background:#0b1220; padding:1rem 1.25rem; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #111827; }
        .nav a { color:#e5e7eb; text-decoration:none; font-weight:700; margin-right:1rem; }
        .container { max-width:1100px; margin:1.25rem auto; padding:0 1.25rem; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:0.65rem; text-align:left; border-bottom:1px solid #1f2937; }
        th { color:#cbd5e1; }
        .btn { padding:0.45rem 0.9rem; border-radius:10px; border:1px solid transparent; cursor:pointer; font-weight:700; }
        .btn-primary { background: var(--main); color:#fff; }
        .btn-ghost { background: transparent; border-color:#1f2937; color:#e5e7eb; }
        @media (max-width: 780px) {
            .nav { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
            .container { padding: 0 1rem; }
            table { display: block; overflow-x: auto; }
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
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
        <h2 style="margin:0;">Danh sách link</h2>
        <a class="btn btn-primary" href="/admin/link_edit.php">+ Tạo link</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Slug</th>
                <th>Click</th>
                <th>Cập nhật</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($links as $link): ?>
            <tr>
                <td><?php echo sanitize($link['title']); ?></td>
                <td><?php echo sanitize($link['slug']); ?></td>
                <td><?php echo (int)$link['clicks']; ?></td>
                <td><?php echo sanitize($link['updated_at'] ?: $link['created_at']); ?></td>
                <td style="text-align:right;">
                    <a class="btn btn-ghost" href="/link.php?slug=<?php echo urlencode($link['slug']); ?>" target="_blank">Xem</a>
                    <a class="btn btn-ghost" href="/admin/link_edit.php?id=<?php echo $link['id']; ?>">Sửa</a>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Xoá link này?');">
                        <input type="hidden" name="delete_id" value="<?php echo $link['id']; ?>">
                        <button class="btn btn-ghost" type="submit">Xoá</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
