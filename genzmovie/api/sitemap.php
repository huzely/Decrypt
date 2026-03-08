<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/xml; charset=utf-8');
$movies = Database::connection()->query('SELECT slug, updated_at FROM movies ORDER BY updated_at DESC')->fetchAll();
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc><?= BASE_URL ?>/</loc></url>
    <?php foreach ($movies as $movie): ?>
    <url>
        <loc><?= BASE_URL ?>/phim/<?= htmlspecialchars($movie['slug']) ?></loc>
        <lastmod><?= date('c', strtotime($movie['updated_at'])) ?></lastmod>
    </url>
    <?php endforeach; ?>
</urlset>
