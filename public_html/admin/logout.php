<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/error_handler.php';

logout_admin();
header('Location: /admin/login.php');
exit;
