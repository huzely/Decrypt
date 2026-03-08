<div class="col-6 col-md-4 col-xl-2 mb-3">
    <a href="/genzmovie/?route=movie&slug=<?= e($movie['slug']) ?>" class="text-decoration-none text-light">
        <div class="card movie-card bg-black border-secondary h-100">
            <img src="<?= e($movie['poster']) ?>" class="card-img-top" alt="<?= e($movie['title']) ?>">
            <div class="card-body p-2">
                <h6 class="card-title small mb-1"><?= e($movie['title']) ?></h6>
                <div class="d-flex justify-content-between small text-secondary">
                    <span><?= e((string) $movie['year']) ?></span>
                    <span><?= e($movie['quality']) ?></span>
                </div>
                <span class="badge bg-danger mt-1"><?= e($movie['language']) ?></span>
            </div>
        </div>
    </a>
</div>
