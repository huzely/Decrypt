<?php
require __DIR__ . '/../app/config/config.php';
require __DIR__ . '/../app/lib/db.php';
require __DIR__ . '/../app/lib/auth.php';
require __DIR__ . '/../app/lib/csrf.php';

start_secure_session($config);
$pdo = db($config);

header('Content-Type: application/json');
$raw = file_get_contents('php://input');
if (empty($_POST) && $raw) {
    parse_str($raw, $_POST);
}

$token = $_POST['token'] ?? '';
if (!csrf_verify($token)) {
    http_response_code(403);
    echo json_encode(['error'=>'forbidden']);
    exit;
}

$type = $_POST['type'] ?? '';
$slug = trim($_POST['slug'] ?? '');
$allowed = ['article_view','ad_forced_redirect','ad_close_click'];
if (!in_array($type, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['error'=>'invalid']);
    exit;
}
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$now = time();
$hashIp = hash_hmac('sha256', $ip, $config['session_salt']);
$hashUa = hash_hmac('sha256', $ua, $config['session_salt']);

$stmt = $pdo->prepare('SELECT COUNT(*) FROM click_events WHERE ip_hash=? AND slug=? AND event_type=? AND created_at >= DATE_SUB(NOW(), INTERVAL ? SECOND)');
$stmt->execute([$hashIp, $slug, $type, $config['rate_limit_seconds']]);
if ($stmt->fetchColumn() > 0) {
    echo json_encode(['status'=>'rate_limited']);
    exit;
}

$insert = $pdo->prepare('INSERT INTO click_events (slug,event_type,ip_hash,ua_hash,token_hash,created_at) VALUES (?,?,?,?,?,NOW())');
$insert->execute([$slug,$type,$hashIp,$hashUa,hash('sha256',$token)]);
echo json_encode(['status'=>'ok']);
