<?php
require_once __DIR__ . '/../config.php';
header('Location: ' . (isAdmin() ? 'dashboard.php' : 'login.php'));
exit;
