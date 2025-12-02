<?php
require_once __DIR__ . '/helpers.php';

$settings = fetch_settings();
$pdo = get_pdo();
$links = $pdo->query('SELECT l.*, COUNT(c.id) as click_count FROM links l LEFT JOIN clicks c ON c.link_id = l.id GROUP BY l.id ORDER BY l.created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($settings['site_title']); ?></title>
    <link rel="stylesheet" href="static/css/style.css">
    <style>
        :root {
            --main: <?php echo sanitize($settings['primary_color']); ?>;
            --accent: <?php echo sanitize($settings['accent_color']); ?>;
        }
        body { background: #f3f4f6; margin: 0; padding: 0; }
        header { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem 1rem; }
        .container { max-width: 1100px; margin: 0 auto; padding: 1rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; }
        .link-card a { text-decoration: none; color: #111827; }
        .meta { font-size: 0.9rem; color: #6b7280; }
        .chip { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 999px; background: #eef2ff; color: #4338ca; font-weight: 600; font-size: 0.85rem; }
        .actions { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
        .btn { padding: 0.55rem 0.9rem; border-radius: 10px; border: 1px solid transparent; cursor: pointer; font-weight: 600; }
        .btn-primary { background: var(--main); color: #fff; }
        .btn-outline { background: #fff; border-color: #e5e7eb; color: #111827; }
        .nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .nav a { color: #111827; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
<header>
    <div class="nav">
        <div>
            <strong><?php echo sanitize($settings['site_title']); ?></strong>
        </div>
        <div>
            <a href="/admin/login.php">Quản trị</a>
        </div>
    </div>
    <div class="hero">
        <h1 style="margin:0 0 0.25rem 0;"><?php echo sanitize($settings['hero_title']); ?></h1>
        <p style="margin:0; opacity: .9;"><?php echo sanitize($settings['hero_subtitle']); ?></p>
    </div>
</header>
<div class="container">
    <?php if (empty($links)): ?>
        <p>Chưa có link nào được bọc.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($links as $link): ?>
                <div class="link-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.35rem;">
                        <span class="chip">Slug: <?php echo sanitize($link['slug']); ?></span>
                        <span class="meta"><?php echo (int)$link['click_count']; ?> lượt click</span>
                    </div>
                    <a href="/link.php?slug=<?php echo urlencode($link['slug']); ?>">
                        <h3 style="margin:0 0 0.25rem 0;"><?php echo sanitize($link['title']); ?></h3>
                        <p class="meta" style="margin:0;">Shopee → Telegram</p>
                    </a>
                    <div class="actions">
                        <a class="btn btn-primary" href="/link.php?slug=<?php echo urlencode($link['slug']); ?>">Mở link</a>
                        <?php $shareUrl = APP_URL . '/link.php?slug=' . urlencode($link['slug']); ?>
                        <button class="btn btn-outline" onclick='navigator.clipboard.writeText(<?php echo json_encode($shareUrl); ?>).then(()=>alert("Đã copy link"));'>Copy</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
