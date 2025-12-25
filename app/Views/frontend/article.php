<?php
use App\Core\Security;
$meta = [
    'title' => $article['meta_title'] ?: $article['title'],
    'description' => $article['meta_description'] ?: mb_substr(strip_tags($article['content']), 0, 150),
    'og_title' => $article['title'],
    'og_description' => $article['meta_description'] ?? '',
    'og_image' => $article['og_image'] ?? '',
];
$categories = $categories ?? [];
include __DIR__ . '/../partials/header.php';
?>
<article class="article-detail" itemscope itemtype="https://schema.org/NewsArticle">
    <h1 itemprop="headline"><?= Security::escape($article['title']) ?></h1>
    <div class="breadcrumbs"><a href="<?= base_url() ?>">Trang chủ</a> › <?= Security::escape($article['category_name'] ?? '') ?></div>
    <div class="meta">Ngày đăng: <time datetime="<?= $article['published_at'] ?>" itemprop="datePublished"><?= human_date($article['published_at']) ?></time></div>
    <div class="article-body" itemprop="articleBody">
        <?= $article['content'] ?>
    </div>
    <div class="share">
        <span>Chia sẻ:</span>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(base_url($article['slug'])) ?>" target="_blank" rel="noopener">Facebook</a>
        <a href="https://t.me/share/url?url=<?= urlencode(base_url($article['slug'])) ?>" target="_blank" rel="noopener">Telegram</a>
    </div>
    <div class="related">
        <h3>Bài liên quan</h3>
        <ul>
            <?php foreach ($related as $item): ?>
                <li><a href="<?= base_url($item['slug']) ?>"><?= Security::escape($item['title']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "NewsArticle",
        "headline": "<?= Security::escape($article['title']) ?>",
        "datePublished": "<?= $article['published_at'] ?>",
        "description": "<?= Security::escape($meta['description']) ?>",
        "mainEntityOfPage": "<?= base_url($article['slug']) ?>"
    }
    </script>
</article>
<?php if (!empty($settings['interstitial_enabled']) && !empty($settings['shopee_link'])): ?>
<div class="interstitial" id="interstitial" data-frequency="<?= $settings['interstitial_frequency'] ?? 1 ?>" data-unit="<?= $settings['interstitial_unit'] ?? 'session' ?>">
    <div class="interstitial-content">
        <h3>Ưu đãi Shopee</h3>
        <p>Nhận ưu đãi mới trên Shopee, sau đó bạn có thể đọc bài ngay.</p>
        <div class="actions">
            <button class="btn-primary" id="btnShopee" data-link="<?= Security::escape($settings['shopee_link']) ?>" data-article="<?= $article['id'] ?>">Đi tới Shopee (Ưu đãi)</button>
            <button class="btn-secondary" id="btnSkip">Vào đọc bài (Bỏ qua)</button>
        </div>
    </div>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../partials/footer.php'; ?>
