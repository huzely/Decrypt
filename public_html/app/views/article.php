<?php require __DIR__ . '/header.php'; ?>
<article class="article-detail" data-article-id="<?php echo (int)$article['id']; ?>" data-session-token="<?php echo e(session_token()); ?>">
    <h1><?php echo e($article['title']); ?></h1>
    <p class="meta">Cập nhật: <?php echo date('d/m/Y H:i', strtotime($article['published_at'])); ?></p>
    <p class="description"><?php echo nl2br(e($article['description'])); ?></p>
    <?php if (!empty($article['media_url'])): ?>
        <div class="media">
            <?php if (preg_match('/\.(mp4|webm)$/', $article['media_url'])): ?>
                <video controls playsinline preload="metadata">
                    <source src="<?php echo e($article['media_url']); ?>" type="video/mp4">
                </video>
            <?php else: ?>
                <img src="<?php echo e($article['media_url']); ?>" alt="Media" loading="lazy">
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="content"><?php echo nl2br(e($article['body'])); ?></div>
</article>

<?php if (!empty($settings['ad_link']) && empty($_GET['ad'])): ?>
<div class="ad-overlay" id="ad-overlay">
    <div class="ad-card">
        <h3><?php echo e($settings['ad_title'] ?? ''); ?></h3>
        <p><?php echo nl2br(e($settings['ad_body'] ?? '')); ?></p>
        <p class="ad-note">Chạm bất kỳ đâu để tiếp tục</p>
    </div>
</div>
<script>
(function() {
    const overlay = document.getElementById('ad-overlay');
    const adLink = <?php echo json_encode($settings['ad_link']); ?>;
    const articleUrl = <?php echo json_encode(base_url('a/' . $article['id'] . '-' . slugify($article['title']))); ?>;
    const token = <?php echo json_encode(session_token()); ?>;
    const articleId = <?php echo (int)$article['id']; ?>;

    function track(eventType) {
        const payload = new URLSearchParams({
            type: eventType,
            article_id: articleId,
            token: token
        });
        navigator.sendBeacon('<?php echo base_url('api/track.php'); ?>', payload);
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            track('ad_forced_redirect');
            window.location.href = adLink;
            window.open(articleUrl + '?ad=0', '_blank');
        });
    }

    track('article_view');
})();
</script>
<?php else: ?>
<script>
(function(){
    const token = <?php echo json_encode(session_token()); ?>;
    const articleId = <?php echo (int)$article['id']; ?>;
    const payload = new URLSearchParams({type: 'article_view', article_id: articleId, token: token});
    navigator.sendBeacon('<?php echo base_url('api/track.php'); ?>', payload);
})();
</script>
<?php endif; ?>

<?php require __DIR__ . '/footer.php'; ?>
