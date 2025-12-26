<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/cache.php';
require_once __DIR__ . '/lib/slugify.php';
require_once __DIR__ . '/lib/rate_limit.php';

$settings = cache_get('site_settings', 300);
if (!$settings) { $settings = db()->query('SELECT * FROM site_settings LIMIT 1')->fetch(); cache_set('site_settings', $settings); }
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;
$offset = ($page - 1) * $perPage;

$cacheKey = 'home_' . $page;
$data = cache_get($cacheKey, 60);
if (!$data) {
    $stmt = db()->prepare('SELECT * FROM articles WHERE status = "public" ORDER BY created_at DESC LIMIT :offset,:limit');
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $articles = $stmt->fetchAll();
    $total = db()->query('SELECT COUNT(*) FROM articles WHERE status="public"')->fetchColumn();
    $data = ['articles'=>$articles,'total'=>$total];
    cache_set($cacheKey, $data);
}
$trackToken = bin2hex(random_bytes(8));
$_SESSION['track_token'] = $trackToken;
include __DIR__ . '/includes/header.php';
?>
<script>window.__trackToken = "<?= $trackToken ?>"; document.body.dataset.pageview='1';</script>
<section class="cards">
<?php foreach ($data['articles'] as $a): ?>
    <article class="card">
        <a href="/<?= htmlspecialchars($a['slug']) ?>">
            <h2><?= htmlspecialchars($a['title']) ?></h2>
            <div class="meta"><?= date('d/m/Y', strtotime($a['created_at'])) ?></div>
            <p><?= htmlspecialchars(mb_substr(strip_tags($a['excerpt'] ?: $a['content']),0,120)) ?>...</p>
        </a>
    </article>
<?php endforeach; ?>
</section>
<?php $pages = ceil(($data['total'] ?? 0)/$perPage); if($pages>1): ?>
<div class="pagination">
    <?php for($i=1;$i<=$pages;$i++): ?>
        <a class="btn btn-ghost" href="/?page=<?= $i ?>">Trang <?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
