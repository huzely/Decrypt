<?php
require_once __DIR__ . '/config.php';

$videoId = (int) ($_POST['video_id'] ?? 0);
$username = trim($_POST['username'] ?? '');
$comment = trim($_POST['comment'] ?? '');

if ($videoId > 0 && $username !== '' && $comment !== '') {
    $username = mb_substr($username, 0, 50);
    $comment = mb_substr($comment, 0, 500);

    $stmt = $pdo->prepare('INSERT INTO comments (video_id, username, comment) VALUES (:video_id, :username, :comment)');
    $stmt->execute([
        'video_id' => $videoId,
        'username' => $username,
        'comment' => $comment,
    ]);
}

header('Location: video.php?id=' . $videoId);
exit;
