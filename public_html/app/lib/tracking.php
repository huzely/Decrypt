<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/helpers.php';

function hashed_fingerprint(string $ip, string $ua): string
{
    global $config;
    return hash_hmac('sha256', $ip . '|' . $ua, $config['SESSION_SALT']);
}

function track_event(string $event, string $slug, string $token): bool
{
    if ($token !== csrf_token() || is_bot_user_agent()) {
        return false;
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $hash = hashed_fingerprint($ip, $ua);

    $pdo = DB::conn();
    $windowSeconds = (int)setting('rate_window_seconds', 60);
    $maxEvents = (int)setting('rate_max_events', 5);
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM click_events WHERE event_type = :e AND slug = :s AND ip_hash = :h AND created_at >= (NOW() - INTERVAL :win SECOND)');
    $stmt->bindValue(':e', $event);
    $stmt->bindValue(':s', $slug);
    $stmt->bindValue(':h', $hash);
    $stmt->bindValue(':win', $windowSeconds, PDO::PARAM_INT);
    $stmt->execute();
    if ((int)$stmt->fetchColumn() > $maxEvents) {
        return false;
    }

    $stmt = $pdo->prepare('INSERT INTO click_events (event_type, slug, ip_hash, session_token) VALUES (:e, :s, :h, :t)');
    $stmt->execute([
        ':e' => $event,
        ':s' => $slug,
        ':h' => $hash,
        ':t' => $token,
    ]);
    return true;
}

function tracking_stats(): array
{
    $pdo = DB::conn();
    $stmt = $pdo->query('SELECT event_type, COUNT(*) as total FROM click_events GROUP BY event_type');
    $out = [];
    foreach ($stmt->fetchAll() as $row) {
        $out[$row['event_type']] = (int)$row['total'];
    }
    return $out;
}

function reset_tracking(): void
{
    DB::conn()->exec('TRUNCATE TABLE click_events');
}
