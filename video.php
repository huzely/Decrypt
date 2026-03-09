<?php
require_once __DIR__ . '/config.php';
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$update = $pdo->prepare('UPDATE videos SET views = views + 1 WHERE id = :id');
$update->execute(['id' => $id]);

$stats = $pdo->prepare('INSERT INTO video_stats (video_id, view_date, daily_views) VALUES (:video_id, CURDATE(), 1) ON DUPLICATE KEY UPDATE daily_views = daily_views + 1');
$stats->execute(['video_id' => $id]);

$stmt = $pdo->prepare('SELECT * FROM videos WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$video = $stmt->fetch();

if (!$video) {
    header('Location: index.php');
    exit;
}

$commentsStmt = $pdo->prepare('SELECT * FROM comments WHERE video_id = :video_id ORDER BY created_at DESC');
$commentsStmt->execute(['video_id' => $id]);
$comments = $commentsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($video['title']) ?> - AVSTube</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="header">
    <h1>AVSTube</h1>
    <p class="lead">Nền tảng chia sẻ video chất lượng cao</p>
</header>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="video-player-wrapper mb-3">
                    <?php if ($video['type'] === 'embed'): ?>
                        <iframe src="<?= e($video['video_url']) ?>" frameborder="0" allowfullscreen></iframe>
                    <?php else: ?>
                        <video controls src="<?= e($video['video_url']) ?>"></video>
                    <?php endif; ?>
                </div>
                <h2><?= e($video['title']) ?></h2>
                <p class="views mb-0"><i class="fa fa-eye"></i> <?= number_format((int) $video['views'] + 1) ?> lượt xem</p>
                <p><?= nl2br(e($video['description'])) ?></p>
            </div>
            <div class="col-lg-4">
                <div class="panel-dark p-3">
                    <h5>Bình luận</h5>
                    <form method="post" action="comment.php" class="mb-3">
                        <input type="hidden" name="video_id" value="<?= (int) $video['id'] ?>">
                        <input class="form-control bg-dark text-light border-secondary mb-2" name="username" placeholder="Tên của bạn" required maxlength="50">
                        <textarea class="form-control bg-dark text-light border-secondary mb-2" name="comment" rows="3" placeholder="Nội dung bình luận" required maxlength="500"></textarea>
                        <button class="btn btn-red w-100" type="submit">Gửi bình luận</button>
                    </form>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-box p-2 mb-2">
                            <strong><?= e($comment['username']) ?></strong>
                            <small class="text-secondary d-block"><?= e($comment['created_at']) ?></small>
                            <p class="mb-0"><?= nl2br(e($comment['comment'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<footer>
    <p>Copyright © 2026 AVSTube. All Rights Reserved.</p>
</footer>
</body>
</html>
