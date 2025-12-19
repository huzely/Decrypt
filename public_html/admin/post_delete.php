<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/posts.php';
require_once __DIR__ . '/../app/lib/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$token = $_GET['csrf'] ?? '';

if ($id && verify_csrf($token)) {
    delete_post($id);
    admin_log('delete_post');
}
header('Location: /admin/posts.php');
exit;
