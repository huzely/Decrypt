<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/error_handler.php';
require_once __DIR__ . '/lib/track.php';

$pdo = get_pdo();
$slug = $_GET['slug'] ?? '';
if (!$slug) {
    http_response_code(404);
    echo 'Bài viết không tồn tại';
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM posts WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    echo 'Bài viết không tồn tại';
    exit;
}

$settings = fetch_settings($pdo);
$bypass = isset($_GET['ad']) && $_GET['ad'] === '0';
$adsEnabled = (int)$settings['ads_enabled'] === 1 && !$bypass && !empty($settings['ad_link']);

// Track view regardless of ad state
record_stat($pdo, 'view', (int)$post['id'], 45);

$images = json_decode($post['images_json'] ?? '[]', true) ?: [];
$videos = json_decode($post['videos_json'] ?? '[]', true) ?: [];
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="container post">
    <h1><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
    <div class="content"><?php echo nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')); ?></div>

    <?php if (!empty($images)): ?>
        <h3>Hình ảnh</h3>
        <div class="media-grid">
            <?php foreach ($images as $img): $img = trim($img); if (!$img) continue; ?>
                <div class="media-item"><img src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" alt=""></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($videos)): ?>
        <h3>Video</h3>
        <div class="media-grid">
            <?php foreach ($videos as $vid): $vid = trim($vid); if (!$vid) continue; ?>
                <div class="media-item">
                    <?php if (preg_match('/\\.(mp4|webm|ogg)(\\?.*)?$/i', $vid)): ?>
                        <video controls src="<?php echo htmlspecialchars($vid, ENT_QUOTES, 'UTF-8'); ?>"></video>
                    <?php elseif (strpos($vid, 'https://t.me/') === 0): ?>
                        <iframe src="<?php echo htmlspecialchars($vid, ENT_QUOTES, 'UTF-8'); ?>" allowfullscreen></iframe>
                    <?php else: ?>
                        <video controls src="<?php echo htmlspecialchars($vid, ENT_QUOTES, 'UTF-8'); ?>"></video>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
window.__SITE = {
    adsEnabled: <?php echo $adsEnabled ? 'true' : 'false'; ?>,
    adLink: <?php echo json_encode($settings['ad_link'] ?? ''); ?>,
    adTitle: <?php echo json_encode($settings['ad_title'] ?? ''); ?>,
    adBody: <?php echo json_encode($settings['ad_body'] ?? ''); ?>,
    currentSlug: <?php echo json_encode($slug); ?>,
    bypass: <?php echo $bypass ? 'true' : 'false'; ?>
};
</script>
<script src="/assets/js/adflow.js"></script>
<script src="/assets/js/track.js"></script>
</body>
</html>
