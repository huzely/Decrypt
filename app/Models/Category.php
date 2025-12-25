<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Category
{
    public static function all(): array
    {
        $stmt = Database::getInstance()->query('SELECT * FROM categories ORDER BY name');
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $name, string $slug): void
    {
        $stmt = Database::getInstance()->prepare('INSERT INTO categories (name, slug) VALUES (:name, :slug)');
        $stmt->execute([':name' => $name, ':slug' => $slug]);
    }
}
