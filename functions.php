<?php
require_once __DIR__ . '/config.php';

function is_logged_in(): bool
{
    return isset($_SESSION['admin_user']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect_with_message(string $url, string $message): void
{
    $_SESSION['flash'] = $message;
    header("Location: {$url}");
    exit;
}

function render_flash(): void
{
    if (!empty($_SESSION['flash'])) {
        echo '<div class="alert">' . escape_html($_SESSION['flash']) . '</div>';
        unset($_SESSION['flash']);
    }
}

function shorten(string $text, int $limit = 100): string
{
    $text = strip_tags($text);
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return mb_substr($text, 0, $limit) . '...';
}

function handle_upload(string $field_name, array $allowed_types = ['image/jpeg', 'image/png']): ?string
{
    if (empty($_FILES[$field_name]['name'])) {
        return null;
    }

    if ($_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES[$field_name]['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed_types, true)) {
        return null;
    }

    $extension = pathinfo($_FILES[$field_name]['name'], PATHINFO_EXTENSION);
    $target_dir = __DIR__ . '/uploads/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $filename = uniqid('upload_', true) . '.' . $extension;
    $target_path = $target_dir . $filename;

    if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $target_path)) {
        return '/uploads/' . $filename;
    }

    return null;
}

function get_paginated_posts(PDO $pdo, int $page = 1, int $per_page = 10, string $search = ''): array
{
    $offset = ($page - 1) * $per_page;
    $params = [];
    $where = 'WHERE 1=1';
    if ($search !== '') {
        $where .= ' AND title LIKE :search';
        $params[':search'] = '%' . $search . '%';
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts {$where}");
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM posts {$where} ORDER BY created_at DESC LIMIT :offset, :per_page");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
    $stmt->execute();

    $posts = $stmt->fetchAll();
    return [$posts, $total];
}

function fetch_post(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $post = $stmt->fetch();
    return $post ?: null;
}
?>
