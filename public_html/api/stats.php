<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/db.php';
header('Content-Type: application/json');
$views = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_type='post_view'")->fetchColumn();
$adClicks = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_type='ad_click'")->fetchColumn();
echo json_encode(['views' => $views, 'ad_clicks' => $adClicks]);
