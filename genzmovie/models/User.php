<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class User
{
    public function create(string $name, string $email, string $password): bool
    {
        $stmt = Database::connection()->prepare('INSERT INTO users (name, email, password, created_at, updated_at) VALUES (:name, :email, :password, NOW(), NOW())');
        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function favorites(int $userId): array
    {
        $stmt = Database::connection()->prepare('SELECT m.* FROM favorites f JOIN movies m ON m.id = f.movie_id WHERE f.user_id = :user_id ORDER BY f.created_at DESC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
