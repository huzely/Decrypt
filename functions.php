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

function notify_telegram(string $message): void
{
    global $pdo, $telegramApiUrl;
    $stmt = $pdo->query('SELECT telegram_chat_id FROM telegram_admins WHERE is_active = 1');
    $chatIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($chatIds as $chatId) {
        $payload = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];
        $ch = curl_init("{$telegramApiUrl}/sendMessage");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        curl_close($ch);
    }
}

function increment_shopee_click(PDO $pdo, int $postId, string $ip, string $userAgent): void
{
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('UPDATE posts SET shopee_click_count = shopee_click_count + 1, last_shopee_click_at = NOW() WHERE id = :id');
        $stmt->execute([':id' => $postId]);

        $logStmt = $pdo->prepare('INSERT INTO click_logs (post_id, clicked_at, ip_address, user_agent) VALUES (:post_id, NOW(), :ip, :ua)');
        $logStmt->execute([
            ':post_id' => $postId,
            ':ip' => substr($ip, 0, 100),
            ':ua' => substr($userAgent, 0, 255)
        ]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function get_daily_stats(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT COUNT(*) AS clicks FROM click_logs WHERE DATE(clicked_at) = CURDATE()");
    $clicks = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) AS published FROM posts WHERE status = 'published' AND DATE(updated_at) = CURDATE()");
    $published = (int)$stmt->fetchColumn();

    return ['clicks_today' => $clicks, 'published_today' => $published];
}

function get_top_posts(PDO $pdo, int $limit = 5): array
{
    $stmt = $pdo->prepare("SELECT id, title, shopee_click_count FROM posts WHERE status = 'published' ORDER BY shopee_click_count DESC, updated_at DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function escape_output(array $data, array $fields): array
{
    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $data[$field] = escape_html($data[$field]);
        }
    }
    return $data;
}
