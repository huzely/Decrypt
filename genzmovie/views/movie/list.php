<?php $metaTitle = 'Kết quả phim'; include __DIR__ . '/../layouts/header.php'; ?>
<h3 class="mb-3">Kết quả: <?= e($keyword ?: 'Tất cả') ?></h3>
<form class="row g-2 mb-3" method="GET" action="/genzmovie/">
    <input type="hidden" name="route" value="filter">
    <div class="col"><input class="form-control" name="genre" placeholder="Thể loại"></div>
    <div class="col"><input class="form-control" name="year" placeholder="Năm"></div>
    <div class="col"><input class="form-control" name="country" placeholder="Quốc gia"></div>
    <div class="col"><select class="form-select" name="quality"><option value="">Chất lượng</option><option>FHD</option><option>HD</option></select></div>
    <div class="col"><button class="btn btn-danger w-100">Lọc phim</button></div>
</form>
<div class="row"><?php foreach ($movies as $movie): include __DIR__ . '/../partials/movie-card.php'; endforeach; ?></div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
