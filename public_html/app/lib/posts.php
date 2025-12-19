<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/slugify.php';

function fetch_posts(int $limit, int $offset, bool $onlyPublic = true): array
{
    $pdo = DB::conn();
    $sql = 'SELECT id, title, slug, excerpt, published_at, is_public FROM articles ';
    if ($onlyPublic) {
        $sql .= 'WHERE is_public = 1 ';
    }
    $sql .= 'ORDER BY published_at DESC LIMIT :lim OFFSET :off';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function count_posts(bool $onlyPublic = true): int
{
    $where = $onlyPublic ? 'WHERE is_public = 1' : '';
    return (int)DB::conn()->query('SELECT COUNT(*) FROM articles ' . $where)->fetchColumn();
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

function create_post(array $data): int
{
    $pdo = DB::conn();
    $stmt = $pdo->prepare('INSERT INTO articles (title, slug, excerpt, content, media_url, is_public, published_at) VALUES (:title, :slug, :excerpt, :content, :media_url, :is_public, :published_at)');
    $stmt->execute([
        ':title' => $data['title'],
        ':slug' => $data['slug'],
        ':excerpt' => $data['excerpt'],
        ':content' => $data['content'],
        ':media_url' => $data['media_url'],
        ':is_public' => (int)$data['is_public'],
        ':published_at' => $data['published_at'] ?: date('Y-m-d H:i:s'),
    ]);
    return (int)$pdo->lastInsertId();
}

function update_post(int $id, array $data): void
{
    $pdo = DB::conn();
    $stmt = $pdo->prepare('UPDATE articles SET title = :title, slug = :slug, excerpt = :excerpt, content = :content, media_url = :media_url, is_public = :is_public, published_at = :published_at WHERE id = :id');
    $stmt->execute([
        ':title' => $data['title'],
        ':slug' => $data['slug'],
        ':excerpt' => $data['excerpt'],
        ':content' => $data['content'],
        ':media_url' => $data['media_url'],
        ':is_public' => (int)$data['is_public'],
        ':published_at' => $data['published_at'] ?: date('Y-m-d H:i:s'),
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
