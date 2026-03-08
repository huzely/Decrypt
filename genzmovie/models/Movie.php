<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class Movie
{
    public function featured(int $limit = 6): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM movies ORDER BY is_featured DESC, updated_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function byCategory(string $category, int $limit = 12): array
    {
        $sql = 'SELECT DISTINCT m.* FROM movies m
                LEFT JOIN movie_genres mg ON mg.movie_id = m.id
                LEFT JOIN genres g ON g.id = mg.genre_id
                WHERE m.type = :category OR g.name = :category
                ORDER BY m.updated_at DESC LIMIT :limit';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':category', $category);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function search(string $keyword): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM movies WHERE title LIKE :keyword OR actors LIKE :keyword OR tags LIKE :keyword ORDER BY updated_at DESC');
        $stmt->execute(['keyword' => "%{$keyword}%"]);
        return $stmt->fetchAll();
    }

    public function filter(array $filters): array
    {
        $sql = 'SELECT DISTINCT m.* FROM movies m LEFT JOIN movie_genres mg ON mg.movie_id = m.id LEFT JOIN genres g ON g.id = mg.genre_id WHERE 1=1';
        $params = [];

        if (!empty($filters['genre'])) {
            $sql .= ' AND g.name = :genre';
            $params['genre'] = $filters['genre'];
        }
        if (!empty($filters['year'])) {
            $sql .= ' AND m.year = :year';
            $params['year'] = (int) $filters['year'];
        }
        if (!empty($filters['country'])) {
            $sql .= ' AND m.country = :country';
            $params['country'] = $filters['country'];
        }
        if (!empty($filters['quality'])) {
            $sql .= ' AND m.quality = :quality';
            $params['quality'] = $filters['quality'];
        }
        $sql .= ' ORDER BY m.updated_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM movies WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $movie = $stmt->fetch();
        return $movie ?: null;
    }

    public function related(int $movieId, string $genresCsv, int $year): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM movies WHERE id != :id AND (FIND_IN_SET(:genres, tags) OR year = :year) ORDER BY updated_at DESC LIMIT 8');
        $stmt->execute(['id' => $movieId, 'genres' => $genresCsv, 'year' => $year]);
        return $stmt->fetchAll();
    }

    public function save(array $data): int
    {
        $sql = 'INSERT INTO movies (title, slug, original_title, description, poster, year, country, director, actors, quality, language, tags, type, is_featured, meta_title, meta_description, meta_keywords, created_at, updated_at)
                VALUES (:title, :slug, :original_title, :description, :poster, :year, :country, :director, :actors, :quality, :language, :tags, :type, :is_featured, :meta_title, :meta_description, :meta_keywords, NOW(), NOW())';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($data);
        return (int) Database::connection()->lastInsertId();
    }
}
