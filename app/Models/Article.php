<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Config;
use PDO;

class Article
{
    public static function paginate(int $page = 1, int $perPage = 10, ?int $categoryId = null, ?string $search = null, bool $includeDrafts = false): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = 'WHERE 1=1';
        if (!$includeDrafts) {
            $where .= ' AND a.status = "published"';
        }
        if ($categoryId) {
            $where .= ' AND a.category_id = :category';
            $params[':category'] = $categoryId;
        }
        if ($search) {
            $where .= ' AND (a.title LIKE :search OR a.slug LIKE :search OR a.tags LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        $sql = "SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id $where ORDER BY a.published_at DESC LIMIT :offset, :limit";
        $stmt = Database::getInstance()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) FROM articles a $where";
        $countStmt = Database::getInstance()->prepare($countSql);
        foreach ($params as $k => $v) {
            $countStmt->bindValue($k, $v);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        return ['items' => $items, 'total' => $total, 'page' => $page, 'perPage' => $perPage];
    }

    public static function findBySlug(string $slug): ?array
    {
        $sql = 'SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id WHERE a.slug = :slug';
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        $article = $stmt->fetch();
        return $article ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare('SELECT * FROM articles WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $article = $stmt->fetch();
        return $article ?: null;
    }

    public static function related(int $categoryId, int $excludeId, int $limit = 4): array
    {
        if ($categoryId > 0) {
            $stmt = Database::getInstance()->prepare('SELECT id, title, slug FROM articles WHERE status = "published" AND category_id = :cat AND id <> :id ORDER BY published_at DESC LIMIT :limit');
            $stmt->bindValue(':cat', $categoryId, PDO::PARAM_INT);
        } else {
            $stmt = Database::getInstance()->prepare('SELECT id, title, slug FROM articles WHERE status = "published" AND id <> :id ORDER BY published_at DESC LIMIT :limit');
        }
        $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $sql = 'INSERT INTO articles (title, slug, meta_title, meta_description, keywords, og_image, content, category_id, tags, status, include_id_tail, published_at, created_at) VALUES (:title, :slug, :meta_title, :meta_description, :keywords, :og_image, :content, :category_id, :tags, :status, :include_id_tail, :published_at, NOW())';
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':meta_title' => $data['meta_title'],
            ':meta_description' => $data['meta_description'],
            ':keywords' => $data['keywords'],
            ':og_image' => $data['og_image'],
            ':content' => $data['content'],
            ':category_id' => $data['category_id'],
            ':tags' => $data['tags'],
            ':status' => $data['status'],
            ':include_id_tail' => $data['include_id_tail'],
            ':published_at' => $data['published_at'],
        ]);
        return (int) Database::getInstance()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $sql = 'UPDATE articles SET title=:title, slug=:slug, meta_title=:meta_title, meta_description=:meta_description, keywords=:keywords, og_image=:og_image, content=:content, category_id=:category_id, tags=:tags, status=:status, include_id_tail=:include_id_tail, published_at=:published_at WHERE id=:id';
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':meta_title' => $data['meta_title'],
            ':meta_description' => $data['meta_description'],
            ':keywords' => $data['keywords'],
            ':og_image' => $data['og_image'],
            ':content' => $data['content'],
            ':category_id' => $data['category_id'],
            ':tags' => $data['tags'],
            ':status' => $data['status'],
            ':include_id_tail' => $data['include_id_tail'],
            ':published_at' => $data['published_at'],
            ':id' => $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::getInstance()->prepare('DELETE FROM articles WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public static function copy(int $id): ?int
    {
        $article = self::findById($id);
        if (!$article) {
            return null;
        }
        unset($article['id']);
        $article['slug'] = $article['slug'] . '-copy-' . rand(100, 999);
        $article['status'] = 'draft';
        return self::create($article);
    }
}
