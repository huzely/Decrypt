<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Movie.php';
require_once __DIR__ . '/../models/Episode.php';
require_once __DIR__ . '/../config/database.php';

class MovieController
{
    public function show(string $slug): void
    {
        $movieModel = new Movie();
        $episodeModel = new Episode();
        $movie = $movieModel->findBySlug($slug);

        if (!$movie) {
            http_response_code(404);
            echo 'Movie not found';
            return;
        }

        $episodes = $episodeModel->byMovie((int) $movie['id']);
        $currentEpisode = $episodes[0] ?? null;
        if (isset($_GET['episode_id'])) {
            $selected = $episodeModel->find((int) $_GET['episode_id']);
            if ($selected && (int) $selected['movie_id'] === (int) $movie['id']) {
                $currentEpisode = $selected;
            }
        }

        $related = $movieModel->related((int) $movie['id'], (string) $movie['tags'], (int) $movie['year']);

        if (current_user_id()) {
            $stmt = Database::connection()->prepare('INSERT INTO watch_history (user_id, movie_id, episode_id, watched_at) VALUES (:user_id, :movie_id, :episode_id, NOW())');
            $stmt->execute([
                'user_id' => current_user_id(),
                'movie_id' => $movie['id'],
                'episode_id' => $currentEpisode['id'] ?? null,
            ]);
        }

        include __DIR__ . '/../views/movie/show.php';
    }

    public function search(): void
    {
        $keyword = trim($_GET['q'] ?? '');
        $movieModel = new Movie();
        $movies = $keyword !== '' ? $movieModel->search($keyword) : [];
        include __DIR__ . '/../views/movie/list.php';
    }

    public function filter(): void
    {
        $movieModel = new Movie();
        $movies = $movieModel->filter([
            'genre' => $_GET['genre'] ?? null,
            'year' => $_GET['year'] ?? null,
            'country' => $_GET['country'] ?? null,
            'quality' => $_GET['quality'] ?? null,
        ]);
        $keyword = 'Lọc phim';
        include __DIR__ . '/../views/movie/list.php';
    }

    public function autocomplete(): void
    {
        header('Content-Type: application/json');
        $keyword = trim($_GET['q'] ?? '');
        $movieModel = new Movie();
        $movies = $keyword !== '' ? array_slice($movieModel->search($keyword), 0, 8) : [];
        echo json_encode(array_map(static fn(array $movie) => ['title' => $movie['title'], 'slug' => $movie['slug']], $movies), JSON_UNESCAPED_UNICODE);
    }
}
