<?php
require_once __DIR__ . '/config.php';

function site_settings(): array {
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM settings ORDER BY id ASC LIMIT 1');
    return $stmt->fetch() ?: [
        'site_name' => 'NightFlix PHP',
        'logo' => '',
        'primary_color' => '#e50914',
        'popup_ads_enabled' => 1,
    ];
}

function esc(?string $value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function format_date(string $date): string {
    return date('M d, Y', strtotime($date));
}

function get_ads(?string $position = null, bool $activeOnly = true): array {
    global $pdo;
    $sql = 'SELECT * FROM ads';
    $conditions = [];
    $params = [];
    if ($position) {
        $conditions[] = 'position = :position';
        $params['position'] = $position;
    }
    if ($activeOnly) {
        $conditions[] = 'active = 1';
    }
    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $sql .= ' ORDER BY id DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function get_announcement(): ?array {
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM announcements WHERE active = 1 ORDER BY id DESC LIMIT 1');
    $row = $stmt->fetch();
    return $row ?: null;
}

function get_categories(): array {
    global $pdo;
    return $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();
}

function get_tags(): array {
    global $pdo;
    return $pdo->query('SELECT * FROM tags ORDER BY name ASC')->fetchAll();
}

function parse_tags(?string $tags): array {
    $parts = array_filter(array_map('trim', explode(',', (string) $tags)));
    return array_values(array_unique($parts));
}

function sync_taxonomy(string $category, string $tags): void {
    global $pdo;
    if ($category !== '') {
        $stmt = $pdo->prepare('INSERT IGNORE INTO categories (name) VALUES (:name)');
        $stmt->execute(['name' => $category]);
    }
    foreach (parse_tags($tags) as $tag) {
        $stmt = $pdo->prepare('INSERT IGNORE INTO tags (name) VALUES (:name)');
        $stmt->execute(['name' => $tag]);
    }
}

function fetch_videos(array $filters = []): array {
    global $pdo;
    $sql = 'SELECT * FROM videos';
    $conditions = [];
    $params = [];

    if (!empty($filters['category'])) {
        $conditions[] = 'category = :category';
        $params['category'] = $filters['category'];
    }
    if (!empty($filters['tag'])) {
        $conditions[] = 'tags LIKE :tag';
        $params['tag'] = '%' . $filters['tag'] . '%';
    }
    if (!empty($filters['q'])) {
        $conditions[] = '(title LIKE :query OR tags LIKE :query)';
        $params['query'] = '%' . $filters['q'] . '%';
    }

    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $sql .= ' ORDER BY created_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetch_video(int $id): ?array {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM videos WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $video = $stmt->fetch();
    return $video ?: null;
}

function increment_video_view(int $id): void {
    global $pdo;
    $pdo->prepare('UPDATE videos SET views = views + 1 WHERE id = :id')->execute(['id' => $id]);
    $pdo->prepare('INSERT INTO view_logs (video_id, ip) VALUES (:video_id, :ip)')->execute([
        'video_id' => $id,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    ]);
}

function fetch_comments(int $videoId): array {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM comments WHERE video_id = :video_id ORDER BY created_at DESC');
    $stmt->execute(['video_id' => $videoId]);
    return $stmt->fetchAll();
}

function fetch_related_videos(array $video): array {
    global $pdo;
    $tags = parse_tags($video['tags']);
    $sql = 'SELECT * FROM videos WHERE id != :id AND (category = :category';
    $params = ['id' => $video['id'], 'category' => $video['category']];
    foreach ($tags as $index => $tag) {
        $key = 'tag' . $index;
        $sql .= " OR tags LIKE :$key";
        $params[$key] = '%' . $tag . '%';
    }
    $sql .= ') ORDER BY views DESC, created_at DESC LIMIT 8';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function add_comment(int $videoId, string $content): void {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO comments (video_id, content) VALUES (:video_id, :content)');
    $stmt->execute(['video_id' => $videoId, 'content' => trim($content)]);
}

function save_video(array $data, ?int $id = null): void {
    global $pdo;
    sync_taxonomy(trim($data['category']), (string) $data['tags']);
    if ($id) {
        $sql = 'UPDATE videos SET title=:title, description=:description, thumbnail=:thumbnail, video_url=:video_url, type=:type, category=:category, tags=:tags WHERE id=:id';
        $data['id'] = $id;
    } else {
        $sql = 'INSERT INTO videos (title, description, thumbnail, video_url, type, category, tags) VALUES (:title, :description, :thumbnail, :video_url, :type, :category, :tags)';
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'title' => trim($data['title']),
        'description' => trim($data['description']),
        'thumbnail' => trim($data['thumbnail']),
        'video_url' => trim($data['video_url']),
        'type' => trim($data['type']),
        'category' => trim($data['category']),
        'tags' => trim($data['tags']),
        'id' => $data['id'] ?? null,
    ]);
}

function require_admin(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function admin_login(string $username, string $password): bool {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM admin WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    }
    return false;
}

function dashboard_stats(): array {
    global $pdo;
    $totalVideos = $pdo->query('SELECT COUNT(*) FROM videos')->fetchColumn();
    $totalViews = $pdo->query('SELECT COALESCE(SUM(views), 0) FROM videos')->fetchColumn();
    $daily = $pdo->query('SELECT DATE(created_at) AS label, COUNT(*) AS count FROM view_logs GROUP BY DATE(created_at) ORDER BY DATE(created_at) DESC LIMIT 10')->fetchAll();
    $monthly = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS label, COUNT(*) AS count FROM view_logs GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY label DESC LIMIT 12")->fetchAll();
    return [
        'total_videos' => (int) $totalVideos,
        'total_views' => (int) $totalViews,
        'daily' => array_reverse($daily),
        'monthly' => array_reverse($monthly),
    ];
}
