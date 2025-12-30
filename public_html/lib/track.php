<?php
require_once __DIR__ . '/db.php';

function client_ip(): string
{
    $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $value = $_SERVER[$key];
            if (strpos($value, ',') !== false) {
                $parts = explode(',', $value);
                $value = trim($parts[0]);
            }
            return $value;
        }
    }
    return '0.0.0.0';
}

function ip_hash(): string
{
    return hash('sha256', APP_KEY . '|' . client_ip());
}

function record_stat(PDO $pdo, string $type, ?int $postId = null, int $windowSeconds = 60): void
{
    $type = $type === 'ad_click' ? 'ad_click' : 'view';
    $hash = ip_hash();
    $threshold = date('Y-m-d H:i:s', time() - $windowSeconds);

    $check = $pdo->prepare('SELECT COUNT(*) FROM stats WHERE type = ? AND ip_hash = ? AND created_at >= ? AND (post_id <=> ?)');
    $check->execute([$type, $hash, $threshold, $postId]);
    if ((int)$check->fetchColumn() > 0) {
        return;
    }

    $insert = $pdo->prepare('INSERT INTO stats (type, post_id, ip_hash, created_at) VALUES (?, ?, ?, NOW())');
    $insert->execute([$type, $postId, $hash]);
}

function count_stats(PDO $pdo, string $type): int
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM stats WHERE type = ?');
    $stmt->execute([$type]);
    return (int)$stmt->fetchColumn();
}

function count_views_by_post(PDO $pdo, int $postId): int
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM stats WHERE type = "view" AND post_id = ?');
    $stmt->execute([$postId]);
    return (int)$stmt->fetchColumn();
}
