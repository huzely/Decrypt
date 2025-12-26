<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
admin_logout();
header('Location: ' . BASE_URL . '/admin/login.php');
