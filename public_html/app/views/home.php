<?php require __DIR__ . '/header.php'; ?>
<section class="articles">
    <h1>Bài viết mới nhất</h1>
    <div class="article-grid">
        <?php foreach ($articles as $article): ?>
            <?php $slug = slugify($article['title']); ?>
            <article class="card">
                <a href="<?php echo base_url('a/' . $article['id'] . '-' . $slug); ?>">
                    <h2><?php echo e($article['title']); ?></h2>
                    <p class="meta">Xuất bản: <?php echo date('d/m/Y H:i', strtotime($article['published_at'])); ?></p>
                    <p><?php echo e($article['description']); ?></p>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
    <?php if ($pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a class="<?php echo $i === $page ? 'active' : ''; ?>" href="<?php echo base_url('?page=' . $i); ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/footer.php'; ?>
