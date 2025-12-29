<?php
require_once __DIR__ . '/lib/error_handler.php';
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/slugify.php';
require_once __DIR__ . '/lib/csrf.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) { http_response_code(404); echo 'Không tìm thấy bài viết'; exit; }

$settings = load_settings($pdo);
$cacheKey = 'post_' . $slug;
$post = cache_get($cacheKey);
if (!$post) {
    $stmt = $pdo->prepare('SELECT * FROM articles WHERE slug = :slug LIMIT 1');
    $stmt->execute([':slug' => $slug]);
    $post = $stmt->fetch();
    if ($post) {
        cache_set($cacheKey, $post, 45);
    }
}

if (!$post) {
    http_response_code(404);
    echo 'Bài viết không tồn tại.';
    exit;
}

$pageTitle = $post['meta_title'] ?: $post['title'];
$pageDescription = $post['meta_description'] ?: $post['excerpt'];
$extraHead = '<meta name="keywords" content="' . e($post['meta_keywords'] ?: 'tin tức') . '">' .
    '<meta property="og:title" content="' . e($pageTitle) . '">' .
    '<meta property="og:description" content="' . e($pageDescription) . '">' .
    '<meta property="og:url" content="' . e(current_url($post['slug'])) . '">';

include __DIR__ . '/includes/header.php';
?>
<article class="card" style="margin-bottom:20px;">
    <h1><?php echo e($post['title']); ?></h1>
    <p style="color:var(--muted); margin-top:-4px;">Cập nhật: <?php echo date('d/m/Y H:i', strtotime($post['updated_at'] ?? $post['created_at'])); ?></p>
    <div class="content">
        <?php echo nl2br(e($post['content'])); ?>
    </div>
    <?php if (!empty($post['telegram_media'])): ?>
        <div class="media-grid" style="display:grid; gap:10px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-top:15px;">
            <?php foreach (explode("\n", $post['telegram_media']) as $media): $media = trim($media); if (!$media) continue; ?>
                <div class="card">
                    <?php if (preg_match('/(\.mp4|telegram\.org\/file)/i', $media)): ?>
                        <video controls preload="metadata" style="width:100%; border-radius:12px;" src="<?php echo e($media); ?>"></video>
                    <?php else: ?>
                        <img loading="lazy" src="<?php echo e($media); ?>" alt="media">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</article>
<script>
const trackingToken = '<?php echo csrf_token(); ?>';
if (typeof trackEvent === 'function') {
    trackEvent('post_view', '<?php echo e($slug); ?>', trackingToken);
}
<?php if (!empty($settings['ads_enabled']) && !empty($settings['ad_link'])): ?>
window.__SITE = {
    adsEnabled: 1,
    adLink: '<?php echo e($settings['ad_link']); ?>',
    adTitle: '<?php echo e($settings['ad_title'] ?? 'Quảng cáo'); ?>',
    adBody: '<?php echo e($settings['ad_body'] ?? 'Nhấn để tiếp tục'); ?>',
    bypass: <?php echo isset($_GET['ad']) && $_GET['ad'] === '0' ? 'true' : 'false'; ?>,
    currentSlug: '<?php echo e($slug); ?>'
};
<?php if (!(isset($_GET['ad']) && $_GET['ad'] === '0')): ?>
var s = document.createElement('script');
s.src = '/assets/js/adflow.js';
document.body.appendChild(s);
<?php endif; ?>
<?php endif; ?>
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
