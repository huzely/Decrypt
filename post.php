<?php
require_once __DIR__ . '/functions.php';

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$play = isset($_GET['play']) ? (int)$_GET['play'] === 1 : false;
$post = fetch_post($pdo, $postId);

if (!$post || $post['status'] !== 'published') {
    http_response_code(404);
    echo 'Bài viết không tồn tại';
    exit;
}

$safe = escape_output($post, ['title', 'meta_title', 'meta_description', 'meta_keywords', 'meta_image']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $safe['meta_title']; ?></title>
    <meta name="description" content="<?= $safe['meta_description']; ?>">
    <meta name="keywords" content="<?= $safe['meta_keywords']; ?>">
    <meta property="og:title" content="<?= $safe['meta_title']; ?>">
    <meta property="og:description" content="<?= $safe['meta_description']; ?>">
    <meta property="og:image" content="<?= escape_html($post['meta_image']); ?>">
    <meta property="og:url" content="<?= escape_html($baseUrl . '/' . $post['id']); ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header>
    <div class="container nav">
        <a href="/">Trang chủ</a>
    </div>
</header>
<main class="container">
    <h1><?= $safe['title']; ?></h1>
    <?php if (!$play): ?>
        <p><?= nl2br(escape_html($post['content'])); ?></p>
        <a class="button open-video" href="#">Xem video</a>
        <div id="ad-overlay">
            <div class="popup">
                <p>Quảng cáo Shopee - nhấn để tiếp tục</p>
                <button id="overlay-close" class="button">Tiếp tục</button>
            </div>
        </div>
    <?php else: ?>
        <div class="video-wrapper">
            <video controls autoplay>
                <source src="<?= escape_html($post['telegram_video_url']); ?>" type="video/mp4">
                Trình duyệt không hỗ trợ video.
            </video>
        </div>
        <?php if (!empty($post['telegram_image_url'])): ?>
            <img src="<?= escape_html($post['telegram_image_url']); ?>" alt="<?= $safe['title']; ?>" style="max-width:100%;margin-top:15px;">
        <?php endif; ?>
        <article><?= $post['content']; ?></article>
    <?php endif; ?>
</main>
<footer>
    <div class="container">© 2024 Portal</div>
</footer>
<script src="/assets/js/main.js"></script>
<script>setupVideoPopup(<?= $post['id']; ?>);</script>
</body>
</html>
