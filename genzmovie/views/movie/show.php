<?php $metaTitle = $movie['meta_title'] ?: $movie['title']; $metaDescription = $movie['meta_description'] ?: $movie['description']; include __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-4"><img class="img-fluid rounded" src="<?= e($movie['poster']) ?>" alt="<?= e($movie['title']) ?>"></div>
    <div class="col-md-8">
        <h1><?= e($movie['title']) ?></h1>
        <p class="text-secondary"><?= e($movie['original_title']) ?></p>
        <p><?= e($movie['description']) ?></p>
        <ul class="list-unstyled small">
            <li>Năm: <?= e((string) $movie['year']) ?></li>
            <li>Quốc gia: <?= e($movie['country']) ?></li>
            <li>Đạo diễn: <?= e($movie['director']) ?></li>
            <li>Diễn viên: <?= e($movie['actors']) ?></li>
        </ul>
        <?php if (current_user_id()): ?>
        <form method="POST" action="/genzmovie/?route=favorite">
            <?= csrf_field() ?>
            <input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>">
            <button class="btn btn-warning">Thêm vào yêu thích</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<section class="my-4">
    <h4>Trình phát</h4>
    <?php if ($currentEpisode): ?>
        <?php if (!empty($currentEpisode['embed_link'])): ?>
            <div class="ratio ratio-16x9"><iframe src="<?= e($currentEpisode['embed_link']) ?>" allowfullscreen></iframe></div>
        <?php else: ?>
            <video controls class="w-100" poster="<?= e($movie['poster']) ?>">
                <source src="<?= e($currentEpisode['mp4_link']) ?>" type="video/mp4">
                <?php if (!empty($currentEpisode['subtitle_link'])): ?><track kind="subtitles" src="<?= e($currentEpisode['subtitle_link']) ?>" srclang="vi" label="Vietnamese"><?php endif; ?>
            </video>
        <?php endif; ?>
    <?php else: ?>
        <p>Chưa có tập phim.</p>
    <?php endif; ?>
</section>

<section>
    <h5>Danh sách tập / server</h5>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($episodes as $ep): ?>
            <a class="btn btn-sm btn-outline-light" href="/genzmovie/?route=movie&slug=<?= e($movie['slug']) ?>&episode_id=<?= (int) $ep['id'] ?>"><?= e($ep['server_name']) ?> - Tập <?= e((string) $ep['episode_number']) ?></a>
        <?php endforeach; ?>
    </div>
</section>

<section class="my-4">
    <h4>Phim liên quan</h4>
    <div class="row"><?php foreach ($related as $movie): include __DIR__ . '/../partials/movie-card.php'; endforeach; ?></div>
</section>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
