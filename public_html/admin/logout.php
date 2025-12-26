<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/auth.php';
logout();
header('Location: /admin/login.php');
exit;
