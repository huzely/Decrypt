<?php
require_once __DIR__ . '/../bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    save_branding($pdo, $_POST);
    $_SESSION['flash'] = 'Đã lưu giao diện mới';
}
header('Location: ' . base_url() . '/admin/dashboard.php');
exit;
