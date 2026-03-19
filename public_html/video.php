<?php
require_once __DIR__ . '/../includes/functions.php';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$video = fetch_video($id);
if (!$video) {
    http_response_code(404);
    exit('Video not found');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['content'])) {
    add_comment($id, $_POST['content']);
    header('Location: video.php?id=' . $id);
    exit;
}
increment_video_view($id);
$video = fetch_video($id);
$comments = fetch_comments($id);
$related = fetch_related_videos($video);
require_once __DIR__ . '/../includes/header.php';
?>
<div class="detail-layout">
    <section class="panel">
        <?php if ($video['type'] === 'mp4'): ?>
            <video class="player" controls src="<?php echo esc($video['video_url']); ?>"></video>
        <?php else: ?>
            <iframe class="player iframe-player" src="<?php echo esc($video['video_url']); ?>" title="<?php echo esc($video['title']); ?>" allowfullscreen></iframe>
        <?php endif; ?>
        <h1><?php echo esc($video['title']); ?></h1>
        <p class="muted"><?php echo number_format((int) $video['views']); ?> views · <?php echo format_date($video['created_at']); ?></p>
        <p><?php echo nl2br(esc($video['description'])); ?></p>
        <div class="chip-row">
            <?php foreach (parse_tags($video['tags']) as $tag): ?>
                <a class="chip" href="index.php?tag=<?php echo urlencode($tag); ?>">#<?php echo esc($tag); ?></a>
            <?php endforeach; ?>
        </div>
        <form method="post" class="comment-form panel nested-panel">
            <h2>Comments</h2>
            <textarea name="content" rows="4" placeholder="Add a comment"></textarea>
            <button type="submit" class="btn-primary">Post Comment</button>
        </form>
        <div class="comment-list">
            <?php foreach ($comments as $comment): ?>
                <article class="comment-item">
                    <p><?php echo esc($comment['content']); ?></p>
                    <span><?php echo format_date($comment['created_at']); ?></span>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <aside class="panel">
        <h2>Related Videos</h2>
        <?php foreach ($related as $item): ?>
            <a class="related-card" href="video.php?id=<?php echo (int) $item['id']; ?>">
                <img src="<?php echo esc($item['thumbnail']); ?>" alt="<?php echo esc($item['title']); ?>">
                <div>
                    <h3><?php echo esc($item['title']); ?></h3>
                    <p class="muted"><?php echo number_format((int) $item['views']); ?> views</p>
                </div>
            </a>
        <?php endforeach; ?>
    </aside>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
