<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function rate_limited(string $eventType, string $slug, string $token): bool {
    global $pdo;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $config = require __DIR__ . '/../config.php';
    $hashIp = hash('sha256', $ip . $config['SESSION_SALT']);
    $hashUa = hash('sha256', $ua . $config['SESSION_SALT']);
    $tokenHash = hash('sha256', $token . $config['SESSION_SALT']);

    $stmt = $pdo->prepare('SELECT COUNT(*) as c FROM events WHERE event_type = :type AND slug = :slug AND ip_hash = :ip AND ua_hash = :ua AND token_hash = :token AND created_at > (NOW() - INTERVAL 30 SECOND)');
    $stmt->execute([
        ':type' => $eventType,
        ':slug' => $slug,
        ':ip' => $hashIp,
        ':ua' => $hashUa,
        ':token' => $tokenHash,
    ]);
    $count = (int)$stmt->fetchColumn();
    if ($count > 0) return true;

    $insert = $pdo->prepare('INSERT INTO events(event_type, slug, ip_hash, ua_hash, token_hash, created_at) VALUES(:type, :slug, :ip, :ua, :token, NOW())');
    $insert->execute([
        ':type' => $eventType,
        ':slug' => $slug,
        ':ip' => $hashIp,
        ':ua' => $hashUa,
        ':token' => $tokenHash,
    ]);
    return false;
}
