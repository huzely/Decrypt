<?php
require_once __DIR__ . '/../helpers/db.php';

class ClickEvent
{
    public static function record(string $type, int $articleId, string $ip, string $sessionToken): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO click_events (event_type, article_id, ip_address, session_token) VALUES (:event_type, :article_id, :ip_address, :session_token)');
        $stmt->execute([
            ':event_type' => $type,
            ':article_id' => $articleId,
            ':ip_address' => $ip,
            ':session_token' => $sessionToken,
        ]);
    }

    public static function countRecent(string $type, string $ip, int $seconds = 60): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM click_events WHERE event_type = :event_type AND ip_address = :ip AND created_at >= (NOW() - INTERVAL :seconds SECOND)');
        $stmt->bindValue(':event_type', $type);
        $stmt->bindValue(':ip', $ip);
        $stmt->bindValue(':seconds', $seconds, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public static function stats(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT event_type, COUNT(*) as total FROM click_events GROUP BY event_type');
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['event_type']] = (int)$row['total'];
        }
        return $result;
    }

    public static function reset(): void
    {
        $pdo = Database::getConnection();
        $pdo->exec('TRUNCATE TABLE click_events');
    }
}
