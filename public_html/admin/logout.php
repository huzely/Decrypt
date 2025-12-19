<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/lib/auth.php';
start_secure_session($config);
admin_logout();
header('Location: /admin/login.php');
exit;
