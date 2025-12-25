<?php
require_once __DIR__ . '/../app/lib/db.php';
header('Content-Type: application/json');
$totals = db()->query('SELECT event_type, COUNT(*) c FROM events GROUP BY event_type')->fetchAll();
echo json_encode($totals);
