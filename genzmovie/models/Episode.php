<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class Episode
{
    public function byMovie(int $movieId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM episodes WHERE movie_id = :movie_id ORDER BY server_name ASC, episode_number ASC');
        $stmt->execute(['movie_id' => $movieId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM episodes WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $episode = $stmt->fetch();
        return $episode ?: null;
    }

    public function save(array $data): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO episodes (movie_id, server_name, episode_number, embed_link, mp4_link, subtitle_link, created_at, updated_at) VALUES (:movie_id, :server_name, :episode_number, :embed_link, :mp4_link, :subtitle_link, NOW(), NOW())');
        $stmt->execute($data);
    }
}
