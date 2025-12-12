<?php
require_once __DIR__ . '/functions.php';
$slugOrId = $_GET['slug_or_id'] ?? ($_GET['id'] ?? null);
if (!$slugOrId && isset($_GET['id'])) {
    $slugOrId = $_GET['id'];
}
if (!$slugOrId) {
    http_response_code(404);
    echo 'Không tìm thấy bài viết';
    exit;
}
$post = fetch_post_by_slug_or_id($slugOrId, true);
if (!$post) {
    http_response_code(404);
    echo 'Không tìm thấy bài viết';
    exit;
}
$skipAd = isset($_GET['skip_ad']) && $_GET['skip_ad'] == '1';
$play = isset($_GET['play']) && $_GET['play'] == '1';
$settings = get_settings();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape_html($post['meta_title'] ?: $post['title']); ?></title>
    <meta name="description" content="<?= escape_html($post['meta_description']); ?>">
    <meta name="keywords" content="<?= escape_html($post['meta_keywords']); ?>">
    <meta property="og:title" content="<?= escape_html($post['meta_title']); ?>">
    <meta property="og:description" content="<?= escape_html($post['meta_description']); ?>">
    <meta property="og:image" content="<?= escape_html($post['meta_image']); ?>">
    <meta property="og:url" content="https://<?= $_SERVER['HTTP_HOST']; ?>/<?= !empty($post['slug']) ? escape_html($post['slug']) : (int)$post['id']; ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body data-post-id="<?= (int)$post['id']; ?>" data-has-shopee="<?= !empty($post['shopee_link']) ? '1' : '0'; ?>" data-autoplay="<?= ($post['type'] === 'video') ? '1' : '0'; ?>" data-skip-ad="<?= $skipAd ? '1' : '0'; ?>" data-play="<?= $play ? '1' : '0'; ?>">
<?php include __DIR__ . '/includes/header.php'; ?>
<article class="post-detail">
    <h1><?= escape_html($post['title']); ?></h1>
    <div class="meta">Loại: <?= escape_html($post['type']); ?> | Cập nhật: <?= escape_html($post['updated_at']); ?></div>
    <?php if (!empty($post['telegram_image_url'])): ?>
        <img src="<?= escape_html($post['telegram_image_url']); ?>" alt="image" class="detail-image">
    <?php endif; ?>
    <?php if (($post['type'] === 'video') && ($play || $skipAd)): ?>
        <video controls autoplay muted playsinline src="<?= escape_html($post['telegram_video_url']); ?>" class="video-player"></video>
    <?php else: ?>
        <?php if ($post['type'] === 'video'): ?>
            <button class="btn primary" id="btnWatch" data-target="<?= '/' . (!empty($post['slug']) ? escape_html($post['slug']) : (int)$post['id']); ?>?skip_ad=1&play=1">Xem video</button>
        <?php endif; ?>
    <?php endif; ?>
    <div class="content"><?= $post['content']; ?></div>
</article>
<?php include __DIR__ . '/includes/footer.php'; ?>
<script>
window.POST_DATA = {
    postId: <?= (int)$post['id']; ?>,
    hasShopee: <?= !empty($post['shopee_link']) ? 'true' : 'false'; ?>,
    skipAd: <?= $skipAd ? 'true' : 'false'; ?>
};
</script>
</body>
</html>
