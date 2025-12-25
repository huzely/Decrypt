<?php
require_once __DIR__ . '/../app/lib/auth.php';
admin_logout();
header('Location: ' . BASE_URL . '/admin/login.php');
