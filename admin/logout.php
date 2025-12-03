<?php
require_once __DIR__ . '/../bootstrap.php';
session_destroy();
header('Location: /admin/login.php');
exit;
