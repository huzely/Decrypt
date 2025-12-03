<?php
require_once __DIR__ . '/../bootstrap.php';
require_login();

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$links = fetch_links($pdo);
$stats = fetch_stats($pdo);
$branding = get_branding($pdo);

?><!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bảng điều khiển</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/static/css/style.css">
    <style><?php echo render_styles($branding); ?></style>
</head>
<body>
<header>
    <h1>Quản trị link</h1>
    <p class="subtitle">Xin chào <?php echo htmlspecialchars($_SESSION['admin_user']); ?> • <a href="<?php echo htmlspecialchars($baseUrl); ?>/admin/logout.php">Đăng xuất</a></p>
</header>
<div class="container">
    <?php if ($flash): ?><div class="alert success"><?php echo htmlspecialchars($flash); ?></div><?php endif; ?>

    <div class="grid cols-2">
        <div class="card">
            <h2>Thống kê</h2>
            <p>Tổng click: <strong><?php echo $stats['total']; ?></strong></p>
            <p>Hôm nay: <strong><?php echo $stats['daily']; ?></strong></p>
            <p>Tháng này: <strong><?php echo $stats['monthly']; ?></strong></p>
            <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/link_edit.php">
                <input type="hidden" name="action" value="reset_all">
                <button class="btn danger" type="submit">Reset tất cả lượt click</button>
            </form>
        </div>
        <div class="card">
            <h2>Tùy chỉnh giao diện</h2>
            <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/settings.php">
                <label>Màu chính</label>
                <input type="color" name="primary_color" value="<?php echo htmlspecialchars($branding['primary_color'] ?? '#0ea5e9'); ?>">
                <label>Màu nhấn</label>
                <input type="color" name="accent_color" value="<?php echo htmlspecialchars($branding['accent_color'] ?? '#0f172a'); ?>">
                <label>Tiêu đề trang chủ</label>
                <input type="text" name="headline" value="<?php echo htmlspecialchars($branding['headline'] ?? 'Bọc link an toàn'); ?>">
                <button class="btn" type="submit">Lưu</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <h2 style="margin:0;">Danh sách link đã bọc</h2>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a class="btn secondary" href="<?php echo htmlspecialchars($baseUrl); ?>/admin/register.php">Thêm tài khoản admin</a>
                <a class="btn" href="<?php echo htmlspecialchars($baseUrl); ?>/admin/link_edit.php">Tạo link mới</a>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Slug</th>
                    <th>Clicks</th>
                    <th>Đã tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($links as $link): ?>
                <tr>
                    <td><?php echo htmlspecialchars($link['slug']); ?></td>
                    <td><?php echo (int)$link['clicks']; ?></td>
                    <td><?php echo htmlspecialchars($link['created_at']); ?></td>
                    <td>
                        <a class="btn secondary" href="<?php echo '/' . htmlspecialchars($link['slug']); ?>" target="_blank">Mở</a>
                        <button class="btn secondary" type="button" onclick="navigator.clipboard.writeText('<?php echo $baseUrl . '/' . htmlspecialchars($link['slug']); ?>')">Sao chép</button>
                        <a class="btn" href="<?php echo htmlspecialchars($baseUrl); ?>/admin/link_edit.php?id=<?php echo $link['id']; ?>">Sửa</a>
                        <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/link_edit.php" style="display:inline" onsubmit="return confirm('Reset lượt click?');">
                            <input type="hidden" name="action" value="reset_one">
                            <input type="hidden" name="id" value="<?php echo $link['id']; ?>">
                            <button class="btn danger" type="submit">Reset</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
