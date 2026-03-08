<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/database.php';

class UserController
{
    public function dashboard(): void
    {
        require_login();
        $user = $_SESSION['user'];
        $userModel = new User();
        $favorites = $userModel->favorites((int) $user['id']);

        $stmt = Database::connection()->prepare('SELECT m.title, m.slug, wh.watched_at FROM watch_history wh JOIN movies m ON m.id = wh.movie_id WHERE wh.user_id = :uid ORDER BY wh.watched_at DESC LIMIT 30');
        $stmt->execute(['uid' => $user['id']]);
        $history = $stmt->fetchAll();

        include __DIR__ . '/../views/user/dashboard.php';
    }

    public function toggleFavorite(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validate_csrf($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            exit('Invalid request');
        }

        $userId = (int) $_SESSION['user']['id'];
        $movieId = (int) ($_POST['movie_id'] ?? 0);
        $pdo = Database::connection();

        $check = $pdo->prepare('SELECT id FROM favorites WHERE user_id = :uid AND movie_id = :mid LIMIT 1');
        $check->execute(['uid' => $userId, 'mid' => $movieId]);
        $found = $check->fetch();

        if ($found) {
            $pdo->prepare('DELETE FROM favorites WHERE id = :id')->execute(['id' => $found['id']]);
        } else {
            $pdo->prepare('INSERT INTO favorites (user_id, movie_id, created_at) VALUES (:uid, :mid, NOW())')->execute(['uid' => $userId, 'mid' => $movieId]);
        }

        redirect('/?route=dashboard');
    }
}
