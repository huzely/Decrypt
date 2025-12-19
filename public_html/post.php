<?php
require __DIR__ . '/includes/header.php';

$slug = trim($_GET['slug'] ?? '', '/');
if ($slug === '') {
    http_response_code(404);
    exit('Not found');
}
$cacheKey = 'post_' . $slug;
if (!isset($_GET['ad']) || $_GET['ad'] !== '0') {
    if ($html = cache_get($config, $cacheKey)) {
        echo str_replace('{SKIP_AD}', '', $html);
        require __DIR__ . '/includes/footer.php';
        return;
    }
}
$stmt = $pdo->prepare('SELECT * FROM articles WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$article = $stmt->fetch();
if (!$article || ($article['status'] !== 'published' && empty($_SESSION['admin_id']))) {
    http_response_code(404);
    exit('Not found');
}
$page_title = $article['meta_title'] ?: $article['title'];
$meta_description = $article['meta_description'] ?: $article['excerpt'];
$meta_keywords = $article['meta_keywords'] ?? '';
$og_image = '';
if (!empty($article['telegram_media'])) {
    $media = json_decode($article['telegram_media'], true);
    if (!empty($media[0]['url']) && $media[0]['type']==='image') {
        $og_image = $media[0]['url'];
    }
}
$og_url = rtrim($config['base_url'], '/') . '/' . $article['slug'];
$skipAd = isset($_GET['ad']) && $_GET['ad'] === '0';
ob_start();
?>
<article class="article" data-slug="<?= htmlspecialchars($article['slug'], ENT_QUOTES); ?>" <?= $skipAd ? 'data-skip-ad="1"' : ''; ?>>
    <h1><?= htmlspecialchars($article['title'], ENT_QUOTES); ?></h1>
    <p class="meta"><?= date('d/m/Y H:i', strtotime($article['created_at'])); ?></p>
    <?php if (!empty($article['telegram_media'])): 
        $media = json_decode($article['telegram_media'], true) ?: [];
        foreach ($media as $m):
            if ($m['type'] === 'image'): ?>
                <img src="<?= htmlspecialchars($m['url'], ENT_QUOTES); ?>" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES); ?>" loading="lazy">
            <?php elseif ($m['type'] === 'video'): ?>
                <video controls src="<?= htmlspecialchars($m['url'], ENT_QUOTES); ?>" preload="metadata"></video>
            <?php endif;
        endforeach;
    endif; ?>
    <div class="content"><?= $article['content']; ?></div>
</article>
<?php
$html = ob_get_clean();
if (!$skipAd) {
    cache_set($config, $cacheKey, $html, $config['cache_ttl']['post']);
}
echo str_replace('{SKIP_AD}', $skipAd ? '1' : '', $html);
require __DIR__ . '/includes/footer.php';
