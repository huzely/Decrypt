<?php
require_once __DIR__ . '/bootstrap.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($path === '' || $path === 'index.php') {
    $branding = get_branding($pdo);
    $links = fetch_links($pdo);
    ?><!doctype html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo htmlspecialchars($branding['headline'] ?? 'Danh sách link'); ?></title>
        <link rel="stylesheet" href="/static/css/style.css">
        <style><?php echo render_styles($branding); ?></style>
    </head>
    <body>
    <header>
        <h1><?php echo htmlspecialchars($branding['headline'] ?? 'Danh sách link'); ?></h1>
        <p class="subtitle">Tất cả link đã bọc</p>
    </header>
    <div class="container">
        <div class="card">
            <div class="grid cols-2">
                <?php foreach ($links as $link): ?>
                    <div class="list-item">
                        <div class="badge">Slug: <?php echo htmlspecialchars($link['slug']); ?></div>
                        <p style="margin:6px 0;">Shopee: <a href="<?php echo htmlspecialchars($link['shopee_url']); ?>" target="_blank">Mở</a></p>
                        <p style="margin:6px 0;">Telegram: <a href="<?php echo htmlspecialchars($link['telegram_url']); ?>" target="_blank">Mở</a></p>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <a class="btn" href="/<?php echo htmlspecialchars($link['slug']); ?>" target="_blank">Đi tới link</a>
                            <button class="btn secondary" onclick="navigator.clipboard.writeText('<?php echo $baseUrl . '/' . htmlspecialchars($link['slug']); ?>')">Sao chép</button>
                        </div>
                        <p class="subtitle" style="margin-top:8px;">Lượt click: <?php echo (int)$link['clicks']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <p style="text-align:center;"><a href="/admin/login.php">Đăng nhập quản trị</a></p>
    </div>
    </body>
    </html>
    <?php
    exit;
}

$slug = $path;
require __DIR__ . '/link.php';
