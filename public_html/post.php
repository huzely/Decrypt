<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/cache.php';
require_once __DIR__ . '/lib/rate_limit.php';
$slug = $_GET['slug'] ?? '';
if (!$slug) { http_response_code(404); exit('Not found'); }

$stmt = db()->prepare('SELECT * FROM articles WHERE slug = :slug AND status="public" LIMIT 1');
$stmt->execute([':slug' => $slug]);
$article = $stmt->fetch();
if (!$article) { http_response_code(404); exit('Not found'); }
$cacheKey = 'post_'.$slug.'_'.$article['updated_at'];
if ($cached = cache_get($cacheKey, 60)) {
    $article = $cached;
} else {
    cache_set($cacheKey, $article);
}

$settings = cache_get('site_settings', 300);
if (!$settings) { $settings = db()->query('SELECT * FROM site_settings LIMIT 1')->fetch(); cache_set('site_settings', $settings); }

$trackToken = bin2hex(random_bytes(8));
$_SESSION['track_token'] = $trackToken;
$adsEnabled = !empty($settings['ads_enabled']) && !empty($settings['ad_link']) && ($_GET['ad'] ?? '1') !== '0';
include __DIR__ . '/includes/header.php';
?>
<!DOCTYPE html>
<meta property="og:title" content="<?= htmlspecialchars($article['meta_title'] ?: $article['title']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($article['meta_description'] ?? '') ?>">
<meta name="keywords" content="<?= htmlspecialchars($article['meta_keywords'] ?? '') ?>">
<script>window.__trackToken="<?= $trackToken ?>";document.body.dataset.postview='1';document.body.dataset.slug='<?= htmlspecialchars($slug) ?>';</script>
<?php if ($adsEnabled): ?>
<div class="interstitial" id="adOverlay" data-slug="<?= htmlspecialchars($slug) ?>" data-link="<?= htmlspecialchars($settings['ad_link']) ?>" data-token="<?= $trackToken ?>">
    <div class="box">
        <h3><?= htmlspecialchars($settings['ad_title'] ?? 'Ưu đãi Shopee') ?></h3>
        <p><?= htmlspecialchars($settings['ad_body'] ?? 'Nhấn để nhận ưu đãi') ?></p>
        <button class="btn btn-primary" id="adClose" aria-label="Tắt quảng cáo">✕ Tắt quảng cáo</button>
    </div>
</div>
<?php endif; ?>
<article class="article-detail">
    <h1><?= htmlspecialchars($article['title']) ?></h1>
    <div class="meta">Ngày đăng: <?= date('d/m/Y H:i', strtotime($article['created_at'])) ?></div>
    <div class="article-body"><?= $article['content'] ?></div>
    <?php if (!empty($article['telegram_media'])): ?>
        <div class="media">
            <?php foreach (explode("\n", $article['telegram_media']) as $m): $m=trim($m); if(!$m) continue; ?>
                <iframe src="<?= htmlspecialchars($m) ?>" loading="lazy"></iframe>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</article>
<?php include __DIR__ . '/includes/footer.php'; ?>
