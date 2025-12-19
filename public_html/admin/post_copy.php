<?php
require __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../app/lib/slugify.php';
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM articles WHERE id=? LIMIT 1');
    $stmt->execute([$id]);
    if ($row = $stmt->fetch()) {
        $newSlug = $row['slug'] . '-copy-' . time();
        $ins = $pdo->prepare('INSERT INTO articles (slug,title,excerpt,content,meta_title,meta_description,meta_keywords,telegram_media,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,NOW(),NOW())');
        $ins->execute([$newSlug,$row['title'].' (Copy)',$row['excerpt'],$row['content'],$row['meta_title'],$row['meta_description'],$row['meta_keywords'],$row['telegram_media'],'draft']);
        cache_clear($config);
    }
}
header('Location: /admin/posts.php');
exit;
