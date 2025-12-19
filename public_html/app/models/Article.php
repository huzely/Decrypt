<?php
require_once __DIR__ . '/../helpers/db.php';
require_once __DIR__ . '/../helpers/util.php';

class Article
{
    public static function latest(int $limit = 10, int $offset = 0): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM articles WHERE is_public = 1 ORDER BY published_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countPublic(): int
    {
        $pdo = Database::getConnection();
        return (int)$pdo->query('SELECT COUNT(*) FROM articles WHERE is_public = 1')->fetchColumn();
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM articles WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO articles (title, description, body, media_url, is_public, published_at) VALUES (:title, :description, :body, :media_url, :is_public, :published_at)');
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':body' => $data['body'],
            ':media_url' => $data['media_url'],
            ':is_public' => (int)$data['is_public'],
            ':published_at' => $data['published_at'] ?? date('Y-m-d H:i:s'),
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE articles SET title = :title, description = :description, body = :body, media_url = :media_url, is_public = :is_public, published_at = :published_at WHERE id = :id');
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':body' => $data['body'],
            ':media_url' => $data['media_url'],
            ':is_public' => (int)$data['is_public'],
            ':published_at' => $data['published_at'] ?? date('Y-m-d H:i:s'),
            ':id' => $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM articles WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public static function copy(int $id): ?int
    {
        $article = self::find($id);
        if (!$article) {
            return null;
        }
        unset($article['id']);
        $article['title'] .= ' (Copy)';
        return self::create($article);
    }
}
