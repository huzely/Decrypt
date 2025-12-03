<?php
session_start();
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

$pdo = connect_db($config);
initialize_tables($pdo, $config);
$baseUrl = base_url();
