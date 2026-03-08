<?php $metaTitle='Dashboard'; include __DIR__ . '/../layouts/header.php'; ?>
<h3>Xin chào, <?= e($user['name']) ?></h3>
<div class="row">
    <div class="col-md-6">
        <h5>Phim yêu thích</h5>
        <ul class="list-group"><?php foreach ($favorites as $m): ?><li class="list-group-item bg-dark text-light"><a class="text-light" href="/genzmovie/?route=movie&slug=<?= e($m['slug']) ?>"><?= e($m['title']) ?></a></li><?php endforeach; ?></ul>
    </div>
    <div class="col-md-6">
        <h5>Lịch sử xem</h5>
        <ul class="list-group"><?php foreach ($history as $h): ?><li class="list-group-item bg-dark text-light"><?= e($h['title']) ?> - <?= e($h['watched_at']) ?></li><?php endforeach; ?></ul>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
