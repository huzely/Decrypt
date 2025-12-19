<?php
require_once __DIR__ . '/../app/lib/tracking.php';
require_once __DIR__ . '/../app/lib/csrf.php';

$event = $_POST['event'] ?? $_GET['event'] ?? '';
$slug = sanitize_text($_POST['slug'] ?? $_GET['slug'] ?? '');
$token = $_POST['token'] ?? $_GET['token'] ?? '';

if (!$event || !$slug) {
    http_response_code(400);
    echo json_encode(['status' => 'error']);
    exit;
}

$ok = track_event($event, $slug, $token);
header('Content-Type: application/json');
echo json_encode(['status' => $ok ? 'ok' : 'ignored']);
