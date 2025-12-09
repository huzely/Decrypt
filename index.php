<?php
require_once __DIR__ . '/functions.php';

$stmt = $pdo->prepare("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 20");
$stmt->execute();
$posts = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<h1>Bài viết mới</h1>
<div class="card-grid">
    <?php foreach ($posts as $post): $safe = escape_output($post, ['title', 'meta_description', 'type']); ?>
        <div class="card">
            <?php if (!empty($post['thumbnail'])): ?>
                <img src="<?= escape_html($post['thumbnail']); ?>" alt="<?= $safe['title']; ?>">
            <?php elseif (!empty($post['telegram_image_url'])): ?>
                <img src="<?= escape_html($post['telegram_image_url']); ?>" alt="<?= $safe['title']; ?>">
            <?php endif; ?>
            <div class="content">
                <div class="type"><?= $safe['type']; ?></div>
                <h3><?= $safe['title']; ?></h3>
                <p><?= escape_html(shorten($post['meta_description'] ?: $post['content'])); ?></p>
                <a class="button" href="post.php?id=<?= (int)$post['id']; ?>">Xem bài</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
