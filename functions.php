<?php
require_once __DIR__ . '/config.php';
session_start();

function escape_html(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    $text = preg_replace('~[^-a-z0-9]+~', '', $text);
    return $text ?: uniqid('post');
}

function get_settings(): array
{
    static $cached = null;
    global $pdo;
    if ($cached === null) {
        $stmt = $pdo->query('SELECT * FROM site_settings WHERE id = 1');
        $cached = $stmt->fetch() ?: [];
    }
    return $cached;
}

function save_settings(array $data): void
{
    global $pdo;
    $stmt = $pdo->prepare('UPDATE site_settings SET site_name=:site_name, logo_url=:logo_url, banner_url=:banner_url, primary_color=:primary_color, footer_text=:footer_text, ad_title=:ad_title, ad_body=:ad_body, updated_at=NOW() WHERE id=1');
    $stmt->execute([
        ':site_name' => $data['site_name'] ?? '',
        ':logo_url' => $data['logo_url'] ?? null,
        ':banner_url' => $data['banner_url'] ?? null,
        ':primary_color' => $data['primary_color'] ?? null,
        ':footer_text' => $data['footer_text'] ?? null,
        ':ad_title' => $data['ad_title'] ?? null,
        ':ad_body' => $data['ad_body'] ?? null,
    ]);
}

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

function render_flash(): void
{
    if (!empty($_SESSION['flash'])) {
        echo '<div class="alert">' . escape_html($_SESSION['flash']) . '</div>';
        unset($_SESSION['flash']);
    }
}

function redirect_with_message(string $url, string $message): void
{
    $_SESSION['flash'] = $message;
    header("Location: {$url}");
    exit;
}

function ensure_unique_slug(PDO $pdo, string $baseSlug, ?int $ignoreId = null): string
{
    $slug = $baseSlug;
    $i = 1;
    while (true) {
        $sql = 'SELECT COUNT(*) FROM posts WHERE slug = :slug';
        $params = [':slug' => $slug];
        if ($ignoreId) {
            $sql .= ' AND id != :id';
            $params[':id'] = $ignoreId;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if ((int)$stmt->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $i;
        $i++;
    }
}

function fetch_post_by_slug_or_id($slugOrId, bool $onlyPublished = true)
{
    global $pdo;
    if (ctype_digit((string)$slugOrId)) {
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = :id' . ($onlyPublished ? ' AND status="published"' : ''));
        $stmt->execute([':id' => (int)$slugOrId]);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE slug = :slug' . ($onlyPublished ? ' AND status="published"' : ''));
        $stmt->execute([':slug' => $slugOrId]);
    }
    return $stmt->fetch();
}

function send_telegram_message(string $text): void
{
    global $pdo;
    $stmt = $pdo->query('SELECT telegram_chat_id FROM telegram_admins WHERE is_active = 1');
    $chatIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($chatIds as $chatId) {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, telegram_api_url('sendMessage'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}

function log_pageview(): void
{
    global $pdo;
    $ip = substr($_SERVER['REMOTE_ADDR'] ?? 'unknown', 0, 100);
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255);
    $today = date('Y-m-d');
    $pdo->prepare('INSERT INTO page_views (visit_date, pageviews, unique_visitors) VALUES (:d,1,0) ON DUPLICATE KEY UPDATE pageviews = pageviews + 1')
        ->execute([':d' => $today]);

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM visitor_logs WHERE visit_date=:d AND ip_address=:ip');
    $stmt->execute([':d' => $today, ':ip' => $ip]);
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->prepare('INSERT INTO visitor_logs (visit_date, ip_address, user_agent, first_seen_at) VALUES (:d,:ip,:ua,NOW())')
            ->execute([':d' => $today, ':ip' => $ip, ':ua' => $ua]);
        $pdo->prepare('UPDATE page_views SET unique_visitors = unique_visitors + 1 WHERE visit_date = :d')
            ->execute([':d' => $today]);
    }
}

function record_visit_path(string $path): void
{
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO visitor_logs (visit_date, ip_address, user_agent, first_seen_at) VALUES (:d,:ip,:ua,NOW()) ON DUPLICATE KEY UPDATE user_agent=:ua');
    $stmt->execute([
        ':d' => date('Y-m-d'),
        ':ip' => substr($_SERVER['REMOTE_ADDR'] ?? 'unknown', 0, 100),
        ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255),
    ]);
}

function is_bot_user_agent(string $ua): bool
{
    $ua = strtolower($ua);
    return $ua === '' || strpos($ua, 'bot') !== false || strpos($ua, 'curl') !== false || strpos($ua, 'spider') !== false || strpos($ua, 'crawler') !== false;
}

function create_ad_token(int $postId): array
{
    global $pdo;
    $token = bin2hex(random_bytes(32));
    $ip = substr($_SERVER['REMOTE_ADDR'] ?? 'unknown', 0, 100);
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255);
    $stmt = $pdo->prepare('INSERT INTO ad_tokens (token, post_id, ip_address, user_agent, created_at, valid_after, is_used) VALUES (:t,:p,:ip,:ua,NOW(), DATE_ADD(NOW(), INTERVAL 2 SECOND), 0)');
    $stmt->execute([
        ':t' => $token,
        ':p' => $postId,
        ':ip' => $ip,
        ':ua' => $ua,
    ]);
    return ['token' => $token];
}

function validate_click_token(int $postId, string $token, string $ip, string $ua): array
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM ad_tokens WHERE token=:t AND post_id=:p AND ip_address=:ip');
    $stmt->execute([':t' => $token, ':p' => $postId, ':ip' => $ip]);
    $row = $stmt->fetch();
    if (!$row) {
        return ['valid' => false, 'reason' => 'token_not_found'];
    }
    if ($row['is_used']) {
        return ['valid' => false, 'reason' => 'token_used'];
    }
    if (strtotime($row['valid_after']) > time()) {
        return ['valid' => false, 'reason' => 'too_fast'];
    }
    if ($row['user_agent'] !== $ua) {
        return ['valid' => false, 'reason' => 'ua_mismatch'];
    }
    if (is_bot_user_agent($ua)) {
        return ['valid' => false, 'reason' => 'bot_ua'];
    }
    $limitStmt = $pdo->prepare('SELECT COUNT(*) FROM click_logs WHERE post_id=:p AND ip_address=:ip AND is_valid=1 AND clicked_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)');
    $limitStmt->execute([':p' => $postId, ':ip' => $ip]);
    if ((int)$limitStmt->fetchColumn() >= 3) {
        return ['valid' => false, 'reason' => 'rate_limit'];
    }
    return ['valid' => true, 'token_id' => $row['id']];
}

function mark_click_valid(int $postId, int $tokenId, string $ip, string $ua, bool $isValid, ?string $reason = null): void
{
    global $pdo;
    if ($isValid) {
        $pdo->prepare('UPDATE ad_tokens SET is_used=1, used_at=NOW() WHERE id=:id')->execute([':id' => $tokenId]);
        $pdo->prepare('UPDATE posts SET shopee_click_count = shopee_click_count + 1, last_shopee_click_at = NOW() WHERE id=:id')->execute([':id' => $postId]);
    }
    $pdo->prepare('INSERT INTO click_logs (post_id, clicked_at, ip_address, user_agent, is_valid, reason) VALUES (:p,NOW(),:ip,:ua,:v,:r)')
        ->execute([
            ':p' => $postId,
            ':ip' => $ip,
            ':ua' => substr($ua, 0, 255),
            ':v' => $isValid ? 1 : 0,
            ':r' => $reason,
        ]);
}

function recent_stats(int $days = 7): array
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT visit_date, pageviews, unique_visitors FROM page_views WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY) ORDER BY visit_date');
    $stmt->execute([':days' => $days]);
    $pv = $stmt->fetchAll();

    $clickStmt = $pdo->prepare('SELECT DATE(clicked_at) as d, SUM(is_valid) as valid_clicks FROM click_logs WHERE clicked_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY) GROUP BY DATE(clicked_at)');
    $clickStmt->execute([':days' => $days]);
    $clicks = [];
    foreach ($clickStmt as $row) {
        $clicks[$row['d']] = (int)$row['valid_clicks'];
    }

    foreach ($pv as &$row) {
        $row['valid_clicks'] = $clicks[$row['visit_date']] ?? 0;
    }
    return $pv;
}

function publish_post_notify(array $post): void
{
    $link = !empty($post['slug']) ? $_SERVER['HTTP_HOST'] . '/' . $post['slug'] : $_SERVER['HTTP_HOST'] . '/' . $post['id'];
    send_telegram_message("Bài mới publish: #{$post['id']} {$post['title']}\nhttps://{$link}");
}
