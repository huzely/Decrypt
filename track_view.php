<?php
require_once __DIR__ . '/functions.php';
log_pageview();
record_visit_path($_SERVER['REQUEST_URI'] ?? '/');
header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);
