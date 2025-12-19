<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/slugify.php';

function fetch_posts(int $limit, int $offset, bool $onlyPublic = true, string $search = '', string $category = ''): array
{
    $pdo = DB::conn();
    $sql = 'SELECT id, title, slug, excerpt, published_at, is_public, category FROM articles ';
    $conds = [];
    $params = [];
    if ($onlyPublic) {
        $conds[] = 'is_public = 1';
    }
    if ($search !== '') {
        $conds[] = '(title LIKE :q OR slug LIKE :q OR tags LIKE :q)';
        $params[':q'] = '%' . $search . '%';
    }
    if ($category !== '') {
        $conds[] = 'category = :cat';
        $params[':cat'] = $category;
    }
    if ($conds) {
        $sql .= 'WHERE ' . implode(' AND ', $conds) . ' ';
    }
    $sql .= 'ORDER BY published_at DESC LIMIT :lim OFFSET :off';
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function count_posts(bool $onlyPublic = true, string $search = '', string $category = ''): int
{
    $sql = 'SELECT COUNT(*) FROM articles ';
    $conds = [];
    $params = [];
    if ($onlyPublic) {
        $conds[] = 'is_public = 1';
    }
    if ($search !== '') {
        $conds[] = '(title LIKE :q OR slug LIKE :q OR tags LIKE :q)';
        $params[':q'] = '%' . $search . '%';
    }
    if ($category !== '') {
        $conds[] = 'category = :cat';
        $params[':cat'] = $category;
    }
    if ($conds) {
        $sql .= 'WHERE ' . implode(' AND ', $conds);
    }
    $stmt = DB::conn()->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->execute();
    return (int)$stmt->fetchColumn();
}

function find_post_by_slug(string $slug): ?array
{
    $pdo = DB::conn();
    $stmt = $pdo->prepare('SELECT * FROM articles WHERE slug = :slug LIMIT 1');
    $stmt->execute([':slug' => $slug]);
    $post = $stmt->fetch();
    return $post ?: null;
}

function find_post_by_id(int $id): ?array
{
    $pdo = DB::conn();
    $stmt = $pdo->prepare('SELECT * FROM articles WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $post = $stmt->fetch();
    return $post ?: null;
}

function related_posts(string $category, int $currentId, int $limit = 3): array
{
    $pdo = DB::conn();
    $stmt = $pdo->prepare('SELECT id, title, slug, published_at FROM articles WHERE is_public = 1 AND category = :cat AND id <> :id ORDER BY published_at DESC LIMIT :lim');
    $stmt->bindValue(':cat', $category);
    $stmt->bindValue(':id', $currentId, PDO::PARAM_INT);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function create_post(array $data): int
{
    $pdo = DB::conn();
    $stmt = $pdo->prepare('INSERT INTO articles (title, slug, excerpt, content, media_url, is_public, published_at, category, tags, meta_title, meta_description, meta_keywords, og_image) VALUES (:title, :slug, :excerpt, :content, :media_url, :is_public, :published_at, :category, :tags, :meta_title, :meta_description, :meta_keywords, :og_image)');
    $stmt->execute([
        ':title' => $data['title'],
        ':slug' => $data['slug'],
        ':excerpt' => $data['excerpt'],
        ':content' => $data['content'],
        ':media_url' => $data['media_url'],
        ':is_public' => (int)$data['is_public'],
        ':published_at' => $data['published_at'] ?: date('Y-m-d H:i:s'),
        ':category' => $data['category'] ?? '',
        ':tags' => $data['tags'] ?? '',
        ':meta_title' => $data['meta_title'] ?? $data['title'],
        ':meta_description' => $data['meta_description'] ?? $data['excerpt'],
        ':meta_keywords' => $data['meta_keywords'] ?? '',
        ':og_image' => $data['og_image'] ?? $data['media_url'] ?? '',
    ]);
    return (int)$pdo->lastInsertId();
}

function update_post(int $id, array $data): void
{
    $pdo = DB::conn();
    $stmt = $pdo->prepare('UPDATE articles SET title = :title, slug = :slug, excerpt = :excerpt, content = :content, media_url = :media_url, is_public = :is_public, published_at = :published_at, category = :category, tags = :tags, meta_title = :meta_title, meta_description = :meta_description, meta_keywords = :meta_keywords, og_image = :og_image WHERE id = :id');
    $stmt->execute([
        ':title' => $data['title'],
        ':slug' => $data['slug'],
        ':excerpt' => $data['excerpt'],
        ':content' => $data['content'],
        ':media_url' => $data['media_url'],
        ':is_public' => (int)$data['is_public'],
        ':published_at' => $data['published_at'] ?: date('Y-m-d H:i:s'),
        ':category' => $data['category'] ?? '',
        ':tags' => $data['tags'] ?? '',
        ':meta_title' => $data['meta_title'] ?? $data['title'],
        ':meta_description' => $data['meta_description'] ?? $data['excerpt'],
        ':meta_keywords' => $data['meta_keywords'] ?? '',
        ':og_image' => $data['og_image'] ?? $data['media_url'] ?? '',
        ':id' => $id,
    ]);
}

function delete_post(int $id): void
{
    $pdo = DB::conn();
    $stmt = $pdo->prepare('DELETE FROM articles WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

function copy_post(int $id): ?int
{
    $post = find_post_by_id($id);
    if (!$post) {
        return null;
    }
    $post['slug'] = unique_slug($post['slug'] . '-copy');
    $post['title'] .= ' (Copy)';
    return create_post($post);
}

function unique_slug(string $title): string
{
    $base = slugify($title);
    $slug = $base;
    $i = 1;
    $pdo = DB::conn();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE slug = :slug');
    while (true) {
        $stmt->execute([':slug' => $slug]);
        if ((int)$stmt->fetchColumn() === 0) {
            break;
        }
        $slug = $base . '-' . $i;
        $i++;
    }
    return $slug;
}
