<?php
require_once __DIR__ . '/functions.php';

$stmt = $pdo->query("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 12");
$posts = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<h2>Bài viết mới nhất</h2>
<div class="posts-grid">
    <?php foreach ($posts as $post): ?>
        <article class="post-card">
            <a href="/post.php?id=<?php echo (int)$post['id']; ?>">
                <?php if (!empty($post['thumbnail'])): ?>
                    <img src="<?php echo escape_html($post['thumbnail']); ?>" alt="<?php echo escape_html($post['title']); ?>">
                <?php endif; ?>
                <div class="post-content">
                    <h3>
                        <?php echo escape_html($post['title']); ?>
                        <?php if ($post['type'] === 'video'): ?>
                            <span class="badge">Video</span>
                        <?php endif; ?>
                    </h3>
                    <p><?php echo escape_html(shorten($post['meta_description'] ?: $post['content'], 100)); ?></p>
                </div>
            </a>
        </article>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
