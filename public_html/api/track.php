<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/rate_limit.php';
require_once __DIR__ . '/../lib/telegram.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!$data) {
    $data = [
        'event' => $_GET['event'] ?? '',
        'slug' => $_GET['slug'] ?? '',
        'token' => $_GET['token'] ?? '',
    ];
}

$event = $data['event'] ?? '';
$slug = $data['slug'] ?? '';
$token = $data['token'] ?? '';

if (!$event || !$slug || !$token || !verify_csrf($token)) {
    http_response_code(400);
    echo 'invalid';
    exit;
}

if (rate_limited($event, $slug, $token)) {
    echo 'rate_limited';
    exit;
}

if ($event === 'ad_click') {
    notifyAdClick($slug);
}

echo 'ok';
