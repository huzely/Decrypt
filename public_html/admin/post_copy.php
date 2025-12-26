<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/slugify.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM articles WHERE id=:id');
$stmt->execute([':id'=>$id]);
$a = $stmt->fetch();
if($a){
    $newSlug = slugify($a['slug'].'-copy-'.rand(100,999));
    $ins = db()->prepare('INSERT INTO articles (slug,title,excerpt,content,telegram_media,meta_title,meta_description,meta_keywords,status) VALUES (:slug,:title,:excerpt,:content,:media,:mt,:md,:mk,:status)');
    $ins->execute([':slug'=>$newSlug,':title'=>$a['title'].' (copy)',':excerpt'=>$a['excerpt'],':content'=>$a['content'],':media'=>$a['telegram_media'],':mt'=>$a['meta_title'],':md'=>$a['meta_description'],':mk'=>$a['meta_keywords'],':status'=>'draft']);
}
header('Location: '.BASE_URL.'/admin/posts.php');
