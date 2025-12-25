<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Stat
{
    public static function trackView(int $articleId, string $fingerprint, array $settings): void
    {
        $cooldown = (int)($settings['view_cooldown_seconds'] ?? 120);
        if (self::recentInteraction($fingerprint, 'view', $cooldown)) {
            return;
        }
        self::insertInteraction($fingerprint, 'view', $articleId);
        self::incrementDaily('views');
        self::incrementArticle($articleId, 'views');
    }

    public static function trackClick(int $articleId, string $fingerprint, array $settings): bool
    {
        $cooldown = (int)($settings['click_cooldown_seconds'] ?? 300);
        if (self::recentInteraction($fingerprint, 'click', $cooldown)) {
            return false;
        }
        self::insertInteraction($fingerprint, 'click', $articleId);
        self::incrementDaily('clicks');
        self::incrementArticle($articleId, 'clicks');
        return true;
    }

    private static function recentInteraction(string $fingerprint, string $type, int $seconds): bool
    {
        $stmt = Database::getInstance()->prepare('SELECT occurred_at FROM interactions WHERE fingerprint = :fp AND type = :type ORDER BY occurred_at DESC LIMIT 1');
        $stmt->execute([':fp' => $fingerprint, ':type' => $type]);
        $time = $stmt->fetchColumn();
        if (!$time) {
            return false;
        }
        return (time() - strtotime($time)) < $seconds;
    }

    private static function insertInteraction(string $fingerprint, string $type, int $articleId): void
    {
        $stmt = Database::getInstance()->prepare('INSERT INTO interactions (fingerprint, type, article_id, occurred_at) VALUES (:fp, :type, :article_id, NOW())');
        $stmt->execute([':fp' => $fingerprint, ':type' => $type, ':article_id' => $articleId]);
    }

    private static function incrementDaily(string $column): void
    {
        $today = date('Y-m-d');
        $sql = "INSERT INTO stats_daily (`date`, views, clicks) VALUES (:date, :views, :clicks) ON DUPLICATE KEY UPDATE $column = $column + 1";
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute([
            ':date' => $today,
            ':views' => $column === 'views' ? 1 : 0,
            ':clicks' => $column === 'clicks' ? 1 : 0,
        ]);
    }

    private static function incrementArticle(int $articleId, string $column): void
    {
        $sql = "INSERT INTO article_stats (article_id, views, clicks) VALUES (:id, :views, :clicks) ON DUPLICATE KEY UPDATE $column = $column + 1";
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute([
            ':id' => $articleId,
            ':views' => $column === 'views' ? 1 : 0,
            ':clicks' => $column === 'clicks' ? 1 : 0,
        ]);
    }

    public static function daily(): array
    {
        $stmt = Database::getInstance()->query('SELECT * FROM stats_daily ORDER BY date DESC LIMIT 30');
        return $stmt->fetchAll();
    }

    public static function totals(): array
    {
        $stmt = Database::getInstance()->query('SELECT SUM(views) as views, SUM(clicks) as clicks FROM stats_daily');
        $totals = $stmt->fetch();
        return $totals ?: ['views' => 0, 'clicks' => 0];
    }

    public static function byArticle(): array
    {
        $stmt = Database::getInstance()->query('SELECT a.id, a.title, s.views, s.clicks FROM articles a LEFT JOIN article_stats s ON s.article_id = a.id ORDER BY (s.views IS NULL), s.views DESC');
        return $stmt->fetchAll();
    }

    public static function reset(): void
    {
        Database::getInstance()->exec('TRUNCATE TABLE stats_daily');
        Database::getInstance()->exec('TRUNCATE TABLE article_stats');
        Database::getInstance()->exec('TRUNCATE TABLE interactions');
    }
}
