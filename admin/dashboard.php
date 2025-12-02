<?php
require_once __DIR__ . '/../helpers.php';
require_login();

$settings = fetch_settings();
$pdo = get_pdo();

$totalLinks = (int)$pdo->query('SELECT COUNT(*) FROM links')->fetchColumn();
$totalClicks = (int)$pdo->query('SELECT COUNT(*) FROM clicks')->fetchColumn();

$daily = $pdo->query("SELECT DATE(created_at) as day, COUNT(*) as total FROM clicks GROUP BY DATE(created_at) ORDER BY day DESC LIMIT 14")->fetchAll();
$monthly = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total FROM clicks GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month DESC LIMIT 6")->fetchAll();
$topLinks = $pdo->query("SELECT l.title, l.slug, COUNT(c.id) as total FROM links l LEFT JOIN clicks c ON c.link_id = l.id GROUP BY l.id ORDER BY total DESC LIMIT 5")->fetchAll();
$latestClicks = $pdo->query("SELECT c.*, l.title, l.slug FROM clicks c JOIN links l ON l.id = c.link_id ORDER BY c.created_at DESC LIMIT 8")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/static/css/style.css">
    <style>
        :root {
            --main: <?php echo sanitize($settings['primary_color']); ?>;
            --accent: <?php echo sanitize($settings['accent_color']); ?>;
        }
        body { font-family: 'Inter', system-ui, sans-serif; background:#0f172a; color:#e5e7eb; margin:0; }
        .nav { background:#0b1220; padding:1rem 1.25rem; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #111827; position:sticky; top:0; }
        .nav a { color:#e5e7eb; text-decoration:none; font-weight:700; margin-right:1rem; }
        .container { max-width:1100px; margin:1.5rem auto; padding:0 1.25rem; }
        .cards { display:grid; gap:1rem; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); }
        .card { background:#111827; border:1px solid #1f2937; border-radius:14px; padding:1rem 1.25rem; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:0.55rem; text-align:left; border-bottom:1px solid #1f2937; }
        th { color:#cbd5e1; font-weight:700; }
        tr:last-child td { border-bottom:none; }
        .pill { display:inline-block; padding:0.15rem 0.5rem; border-radius:999px; background:rgba(99,102,241,.15); color:#c7d2fe; font-weight:700; font-size:0.85rem; }
        .muted { color:#94a3b8; font-size:0.9rem; }
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
        <span style="margin-right:0.75rem;">Xin chào, <?php echo sanitize(current_user()['username']); ?></span>
        <a href="/admin/logout.php">Đăng xuất</a>
    </div>
</div>
<div class="container">
    <div class="cards">
        <div class="card">
            <div class="muted">Tổng link</div>
            <h2 style="margin:0;"><?php echo $totalLinks; ?></h2>
        </div>
        <div class="card">
            <div class="muted">Tổng click</div>
            <h2 style="margin:0;"><?php echo $totalClicks; ?></h2>
        </div>
        <div class="card">
            <div class="muted">Click 7 ngày gần nhất</div>
            <h2 style="margin:0;"><?php echo array_sum(array_column($daily, 'total')); ?></h2>
        </div>
        <div class="card">
            <div class="muted">Ghi chú admin</div>
            <p style="margin:0; color:#cbd5e1;"><?php echo sanitize($settings['admin_note']); ?></p>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px,1fr)); gap:1rem; margin-top:1.25rem;">
        <div class="card">
            <h3 style="margin-top:0;">Click theo ngày</h3>
            <table>
                <thead><tr><th>Ngày</th><th>Click</th></tr></thead>
                <tbody>
                <?php foreach ($daily as $row): ?>
                    <tr><td><?php echo sanitize($row['day']); ?></td><td><?php echo (int)$row['total']; ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card">
            <h3 style="margin-top:0;">Click theo tháng</h3>
            <table>
                <thead><tr><th>Tháng</th><th>Click</th></tr></thead>
                <tbody>
                <?php foreach ($monthly as $row): ?>
                    <tr><td><?php echo sanitize($row['month']); ?></td><td><?php echo (int)$row['total']; ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px,1fr)); gap:1rem; margin-top:1.25rem;">
        <div class="card">
            <h3 style="margin-top:0;">Top link</h3>
            <table>
                <thead><tr><th>Slug</th><th>Click</th></tr></thead>
                <tbody>
                <?php foreach ($topLinks as $row): ?>
                    <tr>
                        <td><?php echo sanitize($row['slug']); ?></td>
                        <td><?php echo (int)$row['total']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card">
            <h3 style="margin-top:0;">Click mới nhất</h3>
            <table>
                <thead><tr><th>Link</th><th>Thông tin</th></tr></thead>
                <tbody>
                <?php foreach ($latestClicks as $click): ?>
                    <tr>
                        <td>
                            <div class="pill"><?php echo sanitize($click['slug']); ?></div>
                            <div class="muted">IP <?php echo sanitize($click['ip_address']); ?></div>
                        </td>
                        <td>
                            <div class="muted"><?php echo sanitize($click['browser']); ?> / <?php echo sanitize($click['os']); ?></div>
                            <div class="muted"><?php echo sanitize($click['created_at']); ?></div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
