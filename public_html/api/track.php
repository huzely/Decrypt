<?php
session_start();
require_once __DIR__ . '/../app/lib/db.php';
require_once __DIR__ . '/../app/lib/rate_limit.php';
header('Content-Type: application/json');
$event = $_POST['event'] ?? $_GET['event'] ?? '';
$slug = $_POST['slug'] ?? $_GET['slug'] ?? null;
$token = $_POST['token'] ?? $_GET['token'] ?? '';
if (!$event || !$token || ($event==='post_view' && !$slug)) { http_response_code(400); echo json_encode(['ok'=>false]); exit; }
if (!hash_equals($_SESSION['track_token'] ?? '', $token)) { http_response_code(403); echo json_encode(['ok'=>false]); exit; }
if (!in_array($event, ['page_view','post_view','ad_click'])) { http_response_code(400); echo json_encode(['ok'=>false]); exit; }
$window = $event==='ad_click' ? 120 : 30;
if (!rate_limit($event, $slug, $window)) { echo json_encode(['ok'=>false,'rate_limited'=>true]); exit; }
log_event($event, $slug, $token);
if ($event==='ad_click') { require_once __DIR__ . '/../app/lib/telegram.php'; queue_click_notify(1); }
echo json_encode(['ok'=>true]);
