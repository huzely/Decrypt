<?php
require_once __DIR__ . '/../helpers.php';
ensure_default_admin();
header('Location: /admin/login.php');
exit;
