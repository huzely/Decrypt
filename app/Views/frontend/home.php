<?php
use App\Core\Security;
$meta = [
    'title' => ($settings['site_name'] ?? 'Tin tức') . ' - Cập nhật mới nhất',
    'description' => $settings['site_description'] ?? 'Bản tin nhanh và chuẩn SEO',
];
include __DIR__ . '/../partials/header.php';
?>
<section class="hero">
    <?php if (!empty($settings['banner'])): ?>
        <img src="<?= base_url('public/' . $settings['banner']) ?>" alt="Banner" class="hero-banner">
    <?php endif; ?>
</section>
<section class="filters">
    <form method="get" action="<?= base_url() ?>" class="filter-form">
        <select name="category">
            <option value="">Tất cả danh mục</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($categoryId == $cat['id']) ? 'selected' : '' ?>><?= Security::escape($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="q" placeholder="Tìm kiếm tiêu đề, slug, tag" value="<?= Security::escape($search ?? '') ?>">
        <button type="submit">Lọc</button>
    </form>
</section>
<section class="articles-grid">
    <?php foreach ($articles as $article): ?>
        <article class="card">
            <a href="<?= base_url($article['slug']) ?>">
                <h2><?= Security::escape($article['title']) ?></h2>
                <p class="meta">Danh mục: <?= Security::escape($article['category_name'] ?? '') ?> · <?= human_date($article['published_at']) ?></p>
                <p><?= Security::escape(mb_substr(strip_tags($article['content']), 0, 140)) ?>...</p>
            </a>
        </article>
    <?php endforeach; ?>
</section>
<div class="pagination">
    <?php
    $totalPages = ceil($pagination['total'] / $pagination['perPage']);
    for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="<?= $i === $pagination['page'] ? 'active' : '' ?>" href="<?= base_url('?page=' . $i) ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
