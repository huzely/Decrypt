<?php
require_once __DIR__ . '/lib/error_handler.php';
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/slugify.php';
require_once __DIR__ . '/lib/csrf.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;
$offset = ($page - 1) * $perPage;

$settings = load_settings($pdo);
$pageTitle = $settings['site_name'] ?? 'Bản tin 24h';
$pageDescription = $settings['site_description'] ?? 'Cập nhật nhanh, chính xác, thân thiện trên mọi thiết bị';

$cacheKey = 'home_' . $page;
$data = cache_get($cacheKey);
if (!$data) {
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM articles WHERE status='public'");
    $total = (int)$totalStmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE status='public' ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $articles = $stmt->fetchAll();
    $data = ['total' => $total, 'articles' => $articles];
    cache_set($cacheKey, $data, 45);
}
$articles = $data['articles'];
$total = $data['total'];
$totalPages = max(1, (int)ceil($total / $perPage));

include __DIR__ . '/includes/header.php';
?>
<section class="card">
    <h1 class="hero-title"><?php echo e($settings['site_name'] ?? 'Bản tin 24h'); ?></h1>
    <p class="hero-desc"><?php echo e($settings['site_description'] ?? 'Cập nhật nhanh, chính xác, thân thiện trên mọi thiết bị'); ?></p>
    <div class="posts-list">
        <?php foreach ($articles as $article): ?>
            <article class="card post-card">
                <a class="article-link" href="/<?php echo e($article['slug']); ?>" data-slug="<?php echo e($article['slug']); ?>">
                    <h3><?php echo e($article['title']); ?></h3>
                    <p><?php echo e($article['excerpt']); ?></p>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
    <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
        <?php if ($page > 1): ?><a class="btn" href="/?page=<?php echo $page-1; ?>">Trang trước</a><?php endif; ?>
        <?php if ($page < $totalPages): ?><a class="btn" href="/?page=<?php echo $page+1; ?>">Trang sau</a><?php endif; ?>
    </div>
</section>
<script>
const trackingToken = '<?php echo csrf_token(); ?>';
if (typeof trackEvent === 'function') {
    trackEvent('page_view', 'home', trackingToken);
}
<?php if (!empty($settings['ads_enabled']) && !empty($settings['ad_link'])): ?>
const articleLinks = document.querySelectorAll('.article-link');
articleLinks.forEach(link => {
    link.addEventListener('click', function(e){
        e.preventDefault();
        window.__SITE = {
            adsEnabled: 1,
            adLink: '<?php echo e($settings['ad_link']); ?>',
            adTitle: '<?php echo e($settings['ad_title'] ?? 'Ưu đãi Shopee'); ?>',
            adBody: '<?php echo e($settings['ad_body'] ?? 'Nhấn để tiếp tục'); ?>',
            bypass: false,
            currentSlug: this.dataset.slug,
            token: trackingToken
        };
        var s = document.createElement('script');
        s.src = '/assets/js/adflow.js?_=' + Date.now();
        document.body.appendChild(s);
    });
});
<?php endif; ?>
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
