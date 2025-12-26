<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../config.php';

function fingerprint(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    return hash('sha256', SESSION_SALT . '|' . $ip . '|' . $ua);
}

function rate_limit(string $event, ?string $slug = null, int $window = 30): bool {
    $fp = fingerprint();
    $stmt = db()->prepare('SELECT created_at FROM events WHERE ip_hash = :fp AND event_type = :event AND (slug = :slug OR (:slug IS NULL AND slug IS NULL)) ORDER BY created_at DESC LIMIT 1');
    $stmt->execute([':fp' => $fp, ':event' => $event, ':slug' => $slug]);
    $last = $stmt->fetchColumn();
    if ($last && (time() - strtotime($last) < $window)) {
        return false;
    }
    return true;
}

function log_event(string $event, ?string $slug, string $token): void {
    $fp = fingerprint();
    $uaHash = hash('sha256', SESSION_SALT . '|' . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));
    $tokenHash = hash('sha256', SESSION_SALT . '|' . $token);
    $stmt = db()->prepare('INSERT INTO events (slug, event_type, ip_hash, ua_hash, token_hash, created_at) VALUES (:slug, :event, :ip, :ua, :token, NOW())');
    $stmt->execute([':slug' => $slug, ':event' => $event, ':ip' => $fp, ':ua' => $uaHash, ':token' => $tokenHash]);
}
