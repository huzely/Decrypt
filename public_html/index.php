<?php
require __DIR__ . '/includes/header.php';
require_once __DIR__ . '/app/lib/slugify.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = $config['pagination'];
$offset = ($page - 1) * $perPage;

$cacheKey = 'home_' . $page;
if ($html = cache_get($config, $cacheKey)) {
    echo $html;
    require __DIR__ . '/includes/footer.php';
    return;
}

$stmt = $pdo->prepare('SELECT SQL_CALC_FOUND_ROWS slug, title, excerpt, created_at FROM articles WHERE status="published" ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();
$total = (int)$pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));

ob_start();
?>
<section class="grid">
<?php foreach ($articles as $a): ?>
    <article class="card">
        <a href="/<?= htmlspecialchars($a['slug'], ENT_QUOTES); ?>" data-article-link="<?= htmlspecialchars($a['slug'], ENT_QUOTES); ?>" class="card-link">
            <h2><?= htmlspecialchars($a['title'], ENT_QUOTES); ?></h2>
            <p><?= htmlspecialchars(mb_strimwidth($a['excerpt'], 0, 140, '...'), ENT_QUOTES); ?></p>
            <div class="meta">
                <span>#<?= (int)$a['slug']; ?></span>
                <span><?= date('d/m/Y', strtotime($a['created_at'])); ?></span>
            </div>
        </a>
    </article>
<?php endforeach; ?>
</section>
<?php if ($pages > 1): ?>
    <nav class="pagination">
        <?php for ($i=1;$i<=$pages;$i++): ?>
            <a href="/?page=<?= $i; ?>" class="<?= $i === $page ? 'active' : ''; ?>"><?= $i; ?></a>
        <?php endfor; ?>
    </nav>
<?php endif; ?>
<?php
$html = ob_get_clean();
echo $html;
cache_set($config, $cacheKey, $html, $config['cache_ttl']['home']);
require __DIR__ . '/includes/footer.php';
