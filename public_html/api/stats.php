<?php
require_once __DIR__ . '/../app/lib/tracking.php';
header('Content-Type: application/json');
echo json_encode(tracking_stats());
