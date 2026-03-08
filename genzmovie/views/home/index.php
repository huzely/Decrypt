<?php $metaTitle = 'GENZMOVIE - Xem phim chất lượng cao'; include __DIR__ . '/../layouts/header.php'; ?>

<div id="featuredCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
    <div class="carousel-inner rounded">
        <?php foreach ($featured as $idx => $movie): ?>
            <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                <img src="<?= e($movie['poster']) ?>" class="d-block w-100 featured-img" alt="<?= e($movie['title']) ?>">
                <div class="carousel-caption text-start bg-overlay p-3 rounded">
                    <h2><?= e($movie['title']) ?></h2>
                    <p><?= e(mb_strimwidth($movie['description'], 0, 160, '...')) ?></p>
                    <a href="/genzmovie/?route=movie&slug=<?= e($movie['slug']) ?>" class="btn btn-danger">Xem ngay</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php foreach ($moviesBySection as $label => $items): ?>
    <section class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4><?= e($label) ?></h4>
            <a class="btn btn-outline-light btn-sm" href="/genzmovie/?route=search&q=<?= urlencode($label) ?>">Xem tất cả</a>
        </div>
        <div class="row">
            <?php foreach ($items as $movie): include __DIR__ . '/../partials/movie-card.php'; endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
