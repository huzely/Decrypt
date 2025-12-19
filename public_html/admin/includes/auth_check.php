<?php
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/lib/auth.php';
require_once __DIR__ . '/../../app/lib/db.php';
start_secure_session($config);
if (empty($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}
$pdo = db($config);
