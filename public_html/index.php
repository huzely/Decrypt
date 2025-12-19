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
$search = sanitize_text($_GET['q'] ?? '');
$category = sanitize_text($_GET['category'] ?? '');
$cacheKey = 'home_' . $page . '_' . md5($search . $category);
$cached = cache_get($cacheKey, $config['CACHE_TTL']['home']);
if ($cached) {
    [$posts, $total] = $cached;
} else {
    $offset = ($page - 1) * $perPage;
    $posts = fetch_posts($perPage, $offset, true, $search, $category);
    $total = count_posts(true, $search, $category);
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
    <form class="search-form" method="get" action="/">
        <input type="text" name="q" placeholder="Tìm kiếm..." value="<?php echo e($search); ?>">
        <select name="category">
            <option value="">Tất cả danh mục</option>
            <?php $cats = ['tin-nong'=>'Tin nóng','phan-tich'=>'Phân tích','doi-song'=>'Đời sống']; foreach ($cats as $k=>$v): ?>
                <option value="<?php echo e($k); ?>" <?php echo $category===$k?'selected':''; ?>><?php echo e($v); ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn" type="submit">Lọc</button>
    </form>
</section>
<section class="articles">
    <div class="article-grid">
        <?php foreach ($posts as $post): ?>
            <article class="card" data-slug="<?php echo e($post['slug']); ?>">
                <a class="post-link" href="<?php echo e(article_url($post)); ?>" data-slug="<?php echo e($post['slug']); ?>" data-title="<?php echo e($post['title']); ?>">
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
                <a class="<?php echo $i === $page ? 'active' : ''; ?>" href="/?page=<?php echo $i; ?><?php echo $search ? '&q='.urlencode($search) : ''; ?><?php echo $category ? '&category='.urlencode($category):''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</section>
<script>
window.__AD__ = {
    link: <?php echo json_encode($settings['ad_link'] ?? ''); ?>,
    title: <?php echo json_encode($settings['ad_title'] ?? ''); ?>,
    body: <?php echo json_encode($settings['ad_body'] ?? ''); ?>,
    token: <?php echo json_encode(csrf_token()); ?>,
    enabled: <?php echo !empty($settings['ad_enabled']) ? 'true':'false'; ?>,
    frequency: <?php echo json_encode($settings['ad_frequency'] ?? 'once'); ?>,
    hours: <?php echo json_encode($settings['ad_interval_hours'] ?? '4'); ?>,
    everyPosts: <?php echo json_encode($settings['ad_every_posts'] ?? '3'); ?>
};
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
