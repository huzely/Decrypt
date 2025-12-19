<?php
require_once __DIR__ . '/app/lib/db.php';
require_once __DIR__ . '/app/lib/helpers.php';
require_once __DIR__ . '/app/lib/posts.php';
require_once __DIR__ . '/app/lib/settings.php';
require_once __DIR__ . '/app/lib/cache.php';
require_once __DIR__ . '/app/lib/csrf.php';

$settings = get_settings();
$pageTitle = $settings['site_name'] ?? 'Tin tức';
$metaDescription = $settings['meta_description'] ?? 'Tin nóng cập nhật 24/7';
$ogImage = $settings['logo_url'] ?? '';

$perPage = 9;
$page = max(1, (int)($_GET['page'] ?? 1));
$cacheKey = 'home_' . $page;
$cached = cache_get($cacheKey, $config['CACHE_TTL']['home']);
if ($cached) {
    [$posts, $total] = $cached;
} else {
    $offset = ($page - 1) * $perPage;
    $posts = fetch_posts($perPage, $offset);
    $total = count_posts();
    cache_set($cacheKey, [$posts, $total]);
}
$pages = (int)ceil($total / $perPage);
include __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div>
        <h1><?php echo e($settings['site_name'] ?? 'Tin nhanh mỗi ngày'); ?></h1>
        <p><?php echo e($settings['hero_text'] ?? 'Tin mới, phân tích sâu và trung lập.'); ?></p>
    </div>
</section>
<section class="articles">
    <div class="article-grid">
        <?php foreach ($posts as $post): ?>
            <article class="card" data-slug="<?php echo e($post['slug']); ?>">
                <a class="post-link" href="/<?php echo e($post['slug']); ?>" data-slug="<?php echo e($post['slug']); ?>" data-title="<?php echo e($post['title']); ?>">
                    <div class="card-body">
                        <h2><?php echo e($post['title']); ?></h2>
                        <p class="meta"><?php echo date('d/m/Y H:i', strtotime($post['published_at'])); ?></p>
                        <p><?php echo e($post['excerpt']); ?></p>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
    <?php if ($pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a class="<?php echo $i === $page ? 'active' : ''; ?>" href="/?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</section>
<script>
window.__AD__ = {
    link: <?php echo json_encode($settings['ad_link'] ?? ''); ?>,
    title: <?php echo json_encode($settings['ad_title'] ?? ''); ?>,
    body: <?php echo json_encode($settings['ad_body'] ?? ''); ?>,
    token: <?php echo json_encode(csrf_token()); ?>
};
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
