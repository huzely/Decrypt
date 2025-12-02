<?php
require_once __DIR__ . '/helpers.php';

$slug = $_GET['slug'] ?? '';
$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT * FROM links WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$link = $stmt->fetch();

if (!$link) {
    http_response_code(404);
    echo 'Link không tồn tại.';
    exit;
}

$settings = fetch_settings();
$clickData = record_click($link);
$telegramMessage = "Click mới trên slug {$link['slug']}";
$telegramMessage .= "\nIP: {$clickData['ip']} ({$clickData['nth']} lần)";
$telegramMessage .= "\nThiết bị: {$clickData['browser']} / {$clickData['os']}";
send_telegram_message($telegramMessage);

$metaImage = link_meta_image_path($link['meta_image']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($link['meta_title'] ?: $link['title']); ?></title>
    <meta name="description" content="<?php echo sanitize($link['meta_description'] ?: $settings['hero_subtitle']); ?>">
    <?php if ($metaImage): ?>
        <meta property="og:image" content="<?php echo sanitize($metaImage); ?>">
        <meta name="twitter:image" content="<?php echo sanitize($metaImage); ?>">
    <?php endif; ?>
    <meta property="og:title" content="<?php echo sanitize($link['meta_title'] ?: $link['title']); ?>">
    <meta property="og:description" content="<?php echo sanitize($link['meta_description'] ?: $settings['hero_subtitle']); ?>">
    <link rel="stylesheet" href="static/css/style.css">
    <style>
        :root {
            --main: <?php echo sanitize($settings['primary_color']); ?>;
            --accent: <?php echo sanitize($settings['accent_color']); ?>;
        }
        body { background: #0f172a; color: #e2e8f0; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .card { max-width: 520px; background: #111827; border-radius: 18px; padding: 2rem; box-shadow: 0 10px 40px rgba(0,0,0,.3); }
        .pill { display:inline-block; padding: 0.3rem 0.7rem; border-radius: 999px; background: rgba(99,102,241,.15); color: #c7d2fe; font-weight:700; font-size:0.85rem; }
        .btn { width: 100%; border: none; border-radius: 12px; padding: 0.9rem; font-weight: 700; cursor: pointer; margin-top: 0.5rem; }
        .btn-primary { background: var(--main); color: #fff; }
        .btn-secondary { background: rgba(255,255,255,0.07); color: #e5e7eb; border: 1px solid rgba(255,255,255,0.08); }
        .progress { height: 8px; background: rgba(255,255,255,0.08); border-radius: 999px; overflow: hidden; margin-top: 1rem; }
        .bar { height: 8px; width: 0; background: var(--accent); transition: width 5s linear; }
        .meta-text { color: #9ca3af; font-size: 0.95rem; }
    </style>
</head>
<body>
<div class="card">
    <span class="pill">Shopee → Telegram</span>
    <h2 style="margin: 0.75rem 0 0.25rem 0; color: #fff;"><?php echo sanitize($link['title']); ?></h2>
    <p class="meta-text">Đang mở Shopee, sau 5 giây sẽ tự mở Telegram...</p>
    <?php if (!empty($link['content_html'])): ?>
        <div style="margin: 0.75rem 0; color: #cbd5e1; line-height:1.6;">
            <?php echo nl2br(sanitize($link['content_html'])); ?>
        </div>
    <?php endif; ?>
    <div class="progress"><div class="bar" id="bar"></div></div>
    <button class="btn btn-primary" onclick="window.location.href='<?php echo sanitize($link['telegram_url']); ?>'">Mở ngay Telegram</button>
    <button class="btn btn-secondary" onclick="window.location.href='<?php echo sanitize($link['shopee_url']); ?>'">Thử lại Shopee</button>
</div>
<script>
    const shopeeUrl = <?php echo json_encode($link['shopee_url']); ?>;
    const telegramUrl = <?php echo json_encode($link['telegram_url']); ?>;
    const bar = document.getElementById('bar');
    setTimeout(() => bar.style.width = '100%', 50);
    window.onload = function() {
        window.location.href = shopeeUrl;
        setTimeout(() => {
            window.location.href = telegramUrl;
        }, 5000);
    };
</script>
</body>
</html>
