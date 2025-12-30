<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_auth();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('DELETE FROM articles WHERE id = :id');
$stmt->execute([':id' => $id]);
redirect('/admin/posts.php');
