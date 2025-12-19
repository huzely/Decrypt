<?php
require_once __DIR__ . '/app/lib/db.php';
require_once __DIR__ . '/app/lib/helpers.php';
require_once __DIR__ . '/app/lib/posts.php';
require_once __DIR__ . '/app/lib/settings.php';
require_once __DIR__ . '/app/lib/cache.php';
require_once __DIR__ . '/app/lib/csrf.php';

$slugParam = sanitize_text($_GET['slug'] ?? '');
if (!$slugParam) {
    http_response_code(404);
    exit('Not found');
}
$slug = $slugParam;
if (preg_match('/-(\\d+)$/', $slugParam, $m)) {
    $slug = preg_replace('/-\\d+$/', '', $slugParam);
    $post = find_post_by_slug($slug);
    if (!$post || (int)$post['id'] !== (int)$m[1]) {
        $post = find_post_by_id((int)$m[1]);
    }
} else {
    $post = cache_get('article_' . $slug, $config['CACHE_TTL']['article']) ?: null;
    if (!$post) {
        $post = find_post_by_slug($slug);
        if ($post) {
            cache_set('article_' . $slug, $post);
        }
    }
}

if (!$post || !$post['is_public']) {
    http_response_code(404);
    exit('Not found');
}

$settings = get_settings();
$pageTitle = ($post['meta_title'] ?: $post['title']) . ' | ' . ($settings['site_name'] ?? 'Tin tức');
$metaDescription = $post['meta_description'] ?: $post['excerpt'];
$ogImage = $post['og_image'] ?: ($post['media_url'] ?? ($settings['logo_url'] ?? ''));
$adParam = $_GET['ad'] ?? null;
$disableAd = ($adParam === '0');

// Frequency control
$showAd = false;
if (!$disableAd && !empty($settings['ad_enabled']) && !empty($settings['ad_link'])) {
    $freq = $settings['ad_frequency'] ?? 'once';
    if (!isset($_SESSION['ad_show_count'])) {
        $_SESSION['ad_show_count'] = 0;
    }
    if (!isset($_SESSION['ad_last_shown'])) {
        $_SESSION['ad_last_shown'] = 0;
    }
    if ($freq === 'once') {
        $showAd = empty($_SESSION['ad_shown_once']);
        $_SESSION['ad_shown_once'] = true;
    } elseif ($freq === 'hourly') {
        $hours = (int)($settings['ad_interval_hours'] ?? 4);
        $showAd = (time() - (int)$_SESSION['ad_last_shown']) > ($hours * 3600);
    } elseif ($freq === 'per_n_posts') {
        $n = max(1, (int)($settings['ad_every_posts'] ?? 3));
        $_SESSION['ad_show_count']++;
        if ($_SESSION['ad_show_count'] >= $n) {
            $showAd = true;
            $_SESSION['ad_show_count'] = 0;
        }
    }
    if ($showAd) {
        $_SESSION['ad_last_shown'] = time();
    }
}

$related = related_posts($post['category'] ?? '', (int)$post['id']);
include __DIR__ . '/includes/header.php';
?>
<article class="article-detail" data-slug="<?php echo e($post['slug']); ?>">
    <div class="breadcrumbs"><a href="/">Trang chủ</a> / <span><?php echo e($post['category'] ?? 'Bài viết'); ?></span></div>
    <h1><?php echo e($post['title']); ?></h1>
    <p class="meta">Cập nhật: <?php echo date('d/m/Y H:i', strtotime($post['published_at'])); ?></p>
    <p class="excerpt"><?php echo nl2br(e($post['excerpt'])); ?></p>
    <?php if (!empty($post['media_url'])): ?>
        <div class="media">
            <?php if (preg_match('/\\.(mp4|webm)$/i', $post['media_url'])): ?>
                <video controls preload="metadata" playsinline>
                    <source src="<?php echo e($post['media_url']); ?>" type="video/mp4">
                </video>
            <?php else: ?>
                <img src="<?php echo e($post['media_url']); ?>" alt="Media" loading="lazy">
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="content"><?php echo nl2br(e($post['content'])); ?></div>
    <div class="share">
        <span>Chia sẻ:</span>
        <a target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(current_url()); ?>">Facebook</a>
        <a target="_blank" rel="noopener" href="https://t.me/share/url?url=<?php echo urlencode(current_url()); ?>">Telegram</a>
    </div>
</article>
<?php if ($related): ?>
<section class="related">
    <h3>Bài liên quan</h3>
    <div class="article-grid">
        <?php foreach ($related as $r): ?>
            <article class="card">
                <a class="post-link" href="<?php echo e(article_url($r)); ?>" data-slug="<?php echo e($r['slug']); ?>">
                    <div class="card-body">
                        <h4><?php echo e($r['title']); ?></h4>
                        <p class="meta"><?php echo date('d/m/Y', strtotime($r['published_at'])); ?></p>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "headline": "<?php echo e($post['title']); ?>",
  "datePublished": "<?php echo e($post['published_at']); ?>",
  "dateModified": "<?php echo e($post['published_at']); ?>",
  "image": "<?php echo e($ogImage); ?>",
  "author": {"@type":"Person","name":"<?php echo e($settings['site_name'] ?? 'News'); ?>"},
  "publisher": {"@type":"Organization","name":"<?php echo e($settings['site_name'] ?? 'News'); ?>","logo":{"@type":"ImageObject","url":"<?php echo e($settings['logo_url'] ?? ''); ?>"}}
}
</script>
<script>
window.__AD__ = {
    link: <?php echo json_encode($settings['ad_link'] ?? ''); ?>,
    title: <?php echo json_encode($settings['ad_title'] ?? ''); ?>,
    body: <?php echo json_encode($settings['ad_body'] ?? ''); ?>,
    token: <?php echo json_encode(csrf_token()); ?>,
    show: <?php echo $showAd ? 'true' : 'false'; ?>,
    enabled: <?php echo !empty($settings['ad_enabled']) ? 'true':'false'; ?>,
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
