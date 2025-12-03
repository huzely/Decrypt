<?php
require_once __DIR__ . '/bootstrap.php';

if (isset($_GET['track'])) {
    $slug = $_GET['slug'] ?? '';
    $link = fetch_link_by_slug($pdo, $slug);
    header('Content-Type: application/json');
    if (!$link) {
        http_response_code(404);
        echo json_encode(['ok' => false]);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS' || $_SERVER['REQUEST_METHOD'] === 'HEAD') {
        echo json_encode(['ok' => true]);
        exit;
    }
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if ($ua === '' || is_bot_request($ua)) {
        echo json_encode(['ok' => true, 'ignored' => true]);
        exit;
    }
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    log_click($pdo, $config, $link, $ua, $ip);
    echo json_encode(['ok' => true]);
    exit;
}

$slug = $slug ?? ($_GET['slug'] ?? '');
$link = $slug ? fetch_link_by_slug($pdo, $slug) : null;
if (!$link) {
    http_response_code(404);
    echo 'Link không tồn tại';
    exit;
}

$branding = get_branding($pdo);
$metaTitle = $link['meta_title'] ?: ($branding['headline'] ?? 'Link được bọc');
$metaDescription = $link['meta_description'] ?: 'Mở link ngay';
$metaImage = $link['meta_image'] ?? '';
?><!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($metaTitle); ?></title>
    <?php if ($metaDescription): ?><meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>"><?php endif; ?>
    <?php if ($metaImage): ?><meta property="og:image" content="<?php echo htmlspecialchars($baseUrl . '/' . $metaImage); ?>"><?php endif; ?>
    <meta property="og:title" content="<?php echo htmlspecialchars($metaTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($baseUrl . '/' . $link['slug']); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/static/css/style.css">
    <style><?php echo render_styles($branding); ?></style>
</head>
<body>
<header>
    <h1>Đang chuyển hướng…</h1>
    <p class="subtitle">Sẽ mở Shopee và tự về Telegram sau 5s</p>
</header>
<div class="container">
    <div class="card" style="text-align:center;">
        <p>Liên kết của bạn: <strong><?php echo htmlspecialchars($link['slug']); ?></strong></p>
        <p>Đang mở Shopee…</p>
        <p>Nếu không tự chuyển, <a href="<?php echo htmlspecialchars($link['telegram_url']); ?>">bấm vào đây để mở Telegram</a>.</p>
    </div>
</div>
<script>
(function(){
    const shopee = <?php echo json_encode($link['shopee_url']); ?>;
    const telegram = <?php echo json_encode($link['telegram_url']); ?>;
    try { window.location.href = shopee; } catch (e) {}
    setTimeout(() => { window.location.href = telegram; }, 5000);
    fetch('<?php echo htmlspecialchars($baseUrl); ?>/link.php?track=1&slug=<?php echo urlencode($link['slug']); ?>', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}});
})();
</script>
</body>
</html>
