<?php
require_once __DIR__ . '/app/lib/db.php';
require_once __DIR__ . '/app/lib/helpers.php';
require_once __DIR__ . '/app/lib/posts.php';
require_once __DIR__ . '/app/lib/settings.php';
require_once __DIR__ . '/app/lib/cache.php';
require_once __DIR__ . '/app/lib/csrf.php';

$slug = sanitize_text($_GET['slug'] ?? '');
if (!$slug) {
    http_response_code(404);
    exit('Not found');
}
$cacheKey = 'article_' . $slug;
$post = cache_get($cacheKey, $config['CACHE_TTL']['article']);
if (!$post) {
    $post = find_post_by_slug($slug);
    if ($post) {
        cache_set($cacheKey, $post);
    }
}

if (!$post || !$post['is_public']) {
    http_response_code(404);
    exit('Not found');
}

$settings = get_settings();
$pageTitle = $post['title'] . ' | ' . ($settings['site_name'] ?? 'Tin tức');
$metaDescription = $post['excerpt'];
$ogImage = $post['media_url'] ?? ($settings['logo_url'] ?? '');
$adParam = $_GET['ad'] ?? null;
$disableAd = ($adParam === '0');
$showAd = !$disableAd && !empty($settings['ad_link']);
include __DIR__ . '/includes/header.php';
?>
<article class="article-detail" data-slug="<?php echo e($post['slug']); ?>">
    <h1><?php echo e($post['title']); ?></h1>
    <p class="meta">Cập nhật: <?php echo date('d/m/Y H:i', strtotime($post['published_at'])); ?></p>
    <p class="excerpt"><?php echo nl2br(e($post['excerpt'])); ?></p>
    <?php if (!empty($post['media_url'])): ?>
        <div class="media">
            <?php if (preg_match('/\.(mp4|webm)$/i', $post['media_url'])): ?>
                <video controls preload="metadata" playsinline>
                    <source src="<?php echo e($post['media_url']); ?>" type="video/mp4">
                </video>
            <?php else: ?>
                <img src="<?php echo e($post['media_url']); ?>" alt="Media" loading="lazy">
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="content"><?php echo nl2br(e($post['content'])); ?></div>
</article>
<script>
window.__AD__ = {
    link: <?php echo json_encode($settings['ad_link'] ?? ''); ?>,
    title: <?php echo json_encode($settings['ad_title'] ?? ''); ?>,
    body: <?php echo json_encode($settings['ad_body'] ?? ''); ?>,
    token: <?php echo json_encode(csrf_token()); ?>,
    show: <?php echo $showAd ? 'true' : 'false'; ?>,
    slug: <?php echo json_encode($post['slug']); ?>
};
</script>
<script>
(function(){
    const payload = new URLSearchParams({event: 'article_view', slug: <?php echo json_encode($post['slug']); ?>, token: <?php echo json_encode(csrf_token()); ?>});
    navigator.sendBeacon('/api/track.php', payload);
})();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
