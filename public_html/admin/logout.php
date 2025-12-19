<?php
require_once __DIR__ . '/../app/lib/auth.php';
logout();
header('Location: /admin/login.php');
exit;
