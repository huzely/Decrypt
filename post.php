<?php
require_once __DIR__ . '/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = fetch_post($pdo, $id);

if (!$post || $post['status'] !== 'published') {
    http_response_code(404);
    echo 'Bài viết không tồn tại';
    exit;
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title><?= escape_html($post['meta_title'] ?: $post['title']); ?></title>
    <meta name="description" content="<?= escape_html($post['meta_description'] ?: shorten($post['content'], 150)); ?>">
    <meta name="keywords" content="<?= escape_html($post['meta_keywords']); ?>">

    <meta property="og:title" content="<?= escape_html($post['meta_title'] ?: $post['title']); ?>">
    <meta property="og:description" content="<?= escape_html($post['meta_description'] ?: shorten($post['content'], 150)); ?>">
    <meta property="og:image" content="<?= escape_html($post['meta_image']); ?>">
    <meta property="og:url" content="<?= 'https://yourdomain.com/' . $post['id']; ?>">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<article class="post-detail">
    <h1><?= escape_html($post['title']); ?></h1>
    <?php if (!empty($post['thumbnail'])): ?>
        <img class="post-thumb" src="<?= escape_html($post['thumbnail']); ?>" alt="<?= escape_html($post['title']); ?>">
    <?php endif; ?>
    <div class="post-body">
        <?= $post['content']; ?>
    </div>

    <?php if (!empty($post['video_url'])): ?>
        <button class="video-btn" data-url="<?= escape_html($post['video_url']); ?>">Xem video</button>
    <?php endif; ?>
</article>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script>
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('video-btn')) {
            e.preventDefault();
            const url = e.target.getAttribute('data-url');
            window.open(url, '_blank');
        }
    });
</script>
</body>
</html>
