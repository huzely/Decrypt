<?php
require_once __DIR__ . '/../bootstrap.php';
session_destroy();
header('Location: ' . base_url() . '/admin/login.php');
exit;
