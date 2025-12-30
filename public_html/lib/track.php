<?php
require_once __DIR__ . '/db.php';

function client_ip(): string
{
    foreach (['HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR'] as $key) {
        if (!empty($_SERVER[$key])) {
            $val = $_SERVER[$key];
            if (strpos($val, ',') !== false) {
                $val = trim(explode(',', $val)[0]);
            }
            return $val;
        }
    }
    return '0.0.0.0';
}

function ip_hash(): string
{
    return hash('sha256', APP_KEY . '|' . client_ip());
}

function record_stat(PDO $pdo, string $type, ?int $postId = null, int $window = 60): void
{
    $type = $type === 'ad_click' ? 'ad_click' : 'view';
    $hash = ip_hash();
    $check = $pdo->prepare('SELECT COUNT(*) FROM stats WHERE type = ? AND ip_hash = ? AND created_at >= (NOW() - INTERVAL ? SECOND) AND (post_id <=> ?)');
    $check->execute([$type, $hash, $window, $postId]);
    if ((int)$check->fetchColumn() > 0) {
        return;
    }
    $insert = $pdo->prepare('INSERT INTO stats (type, post_id, ip_hash, created_at) VALUES (?, ?, ?, NOW())');
    $insert->execute([$type, $postId, $hash]);
}

function stat_total(PDO $pdo, string $type): int
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM stats WHERE type = ?');
    $stmt->execute([$type]);
    return (int)$stmt->fetchColumn();
}
