<?php
require_once __DIR__ . '/functions.php';
$settings = get_settings();
$stmt = $pdo->query('SELECT * FROM posts WHERE status="published" ORDER BY created_at DESC LIMIT 12');
$posts = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="post-grid">
    <?php foreach ($posts as $post): ?>
        <article class="card" data-post-id="<?= (int)$post['id']; ?>" data-has-shopee="<?= !empty($post['shopee_link']) ? '1' : '0'; ?>" data-url="<?= '/' . (!empty($post['slug']) ? escape_html($post['slug']) : (int)$post['id']); ?>">
            <a href="<?= '/' . (!empty($post['slug']) ? escape_html($post['slug']) : (int)$post['id']); ?>" class="card-link">
                <?php if (!empty($post['thumbnail'])): ?>
                    <img src="<?= escape_html($post['thumbnail']); ?>" alt="thumb" class="thumb">
                <?php endif; ?>
                <div class="card-body">
                    <h3><?= escape_html($post['title']); ?><?= $post['type'] === 'video' ? ' 🔴' : ''; ?></h3>
                    <p><?= escape_html(mb_substr(strip_tags($post['meta_description'] ?: $post['content']), 0, 100)); ?></p>
                </div>
            </a>
        </article>
    <?php endforeach; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
