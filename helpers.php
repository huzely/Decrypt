<?php
function base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    $path = $path === '/' ? '' : $path;
    return $scheme . '://' . $host . $path;
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

function login(PDO $pdo, string $username, string $password): bool
{
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_user'] = $admin['username'];
        return true;
    }
    return false;
}

function create_admin(PDO $pdo, string $username, string $password): array
{
    if ($username === '' || $password === '') {
        return [false, 'Thiếu tên tài khoản hoặc mật khẩu'];
    }
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        return [false, 'Tài khoản đã tồn tại'];
    }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
    $stmt->execute([$username, $hash]);
    return [true, 'Đã tạo tài khoản quản trị mới'];
}

function is_bot_request(string $userAgent): bool
{
    $ua = strtolower($userAgent);
    $bots = ['bot', 'crawl', 'slurp', 'spider', 'facebookexternalhit', 'telegrambot', 'facebot', 'vkshare', 'zalo', 'zbot', 'preview'];
    foreach ($bots as $bot) {
        if (strpos($ua, $bot) !== false) {
            return true;
        }
    }
    return false;
}

function parse_user_agent(string $userAgent): array
{
    $browser = 'Khác';
    $os = 'Khác';
    if (stripos($userAgent, 'Windows') !== false) $os = 'Windows';
    elseif (stripos($userAgent, 'Android') !== false) $os = 'Android';
    elseif (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) $os = 'iOS';
    elseif (stripos($userAgent, 'Mac OS') !== false) $os = 'macOS';
    elseif (stripos($userAgent, 'Linux') !== false) $os = 'Linux';

    if (stripos($userAgent, 'Chrome') !== false && stripos($userAgent, 'Edge') === false) $browser = 'Chrome';
    elseif (stripos($userAgent, 'Safari') !== false && stripos($userAgent, 'Chrome') === false) $browser = 'Safari';
    elseif (stripos($userAgent, 'Firefox') !== false) $browser = 'Firefox';
    elseif (stripos($userAgent, 'Edge') !== false) $browser = 'Edge';
    elseif (stripos($userAgent, 'Opera') !== false || stripos($userAgent, 'OPR') !== false) $browser = 'Opera';

    return [$browser, $os];
}

function send_telegram(array $config, string $message): void
{
    if (empty($config['telegram_bot_token']) || empty($config['telegram_chat_id'])) {
        return;
    }
    $url = 'https://api.telegram.org/bot' . $config['telegram_bot_token'] . '/sendMessage';
    $payload = [
        'chat_id' => $config['telegram_chat_id'],
        'text' => $message,
        'parse_mode' => 'HTML',
    ];
    @file_get_contents($url, false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($payload),
            'timeout' => 5,
        ],
    ]));
}

function get_branding(PDO $pdo): array
{
    $stmt = $pdo->prepare('SELECT value FROM settings WHERE name = ?');
    $stmt->execute(['branding']);
    $row = $stmt->fetch();
    return $row ? json_decode($row['value'], true) : ['primary_color' => '#0ea5e9', 'accent_color' => '#111827', 'headline' => 'Bọc link an toàn'];
}

function save_branding(PDO $pdo, array $data): void
{
    $branding = [
        'primary_color' => $data['primary_color'] ?? '#0ea5e9',
        'accent_color' => $data['accent_color'] ?? '#111827',
        'headline' => $data['headline'] ?? 'Bọc link an toàn',
    ];
    upsert_setting($pdo, 'branding', json_encode($branding, JSON_UNESCAPED_UNICODE));
}

function upsert_setting(PDO $pdo, string $name, string $value): void
{
    $stmt = $pdo->prepare('INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)');
    $stmt->execute([$name, $value]);
}

function fetch_links(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM links ORDER BY created_at DESC')->fetchAll();
}

function fetch_link_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM links WHERE slug = ?');
    $stmt->execute([$slug]);
    $link = $stmt->fetch();
    return $link ?: null;
}

function fetch_link(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM links WHERE id = ?');
    $stmt->execute([$id]);
    $link = $stmt->fetch();
    return $link ?: null;
}

function save_link(PDO $pdo, array $payload): array
{
    $slug = trim($payload['slug'] ?? '');
    if ($slug === '') {
        return [false, 'Slug không được trống'];
    }
    $id = isset($payload['id']) ? (int)$payload['id'] : 0;
    $stmt = $pdo->prepare('SELECT id FROM links WHERE slug = ? AND id != ?');
    $stmt->execute([$slug, $id]);
    if ($stmt->fetch()) {
        return [false, 'Slug đã tồn tại'];
    }

    $metaImage = $payload['meta_image_old'] ?? null;
    if (!empty($_FILES['meta_image']['tmp_name'])) {
        $metaImage = save_upload($_FILES['meta_image']);
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE links SET slug=?, shopee_url=?, telegram_url=?, meta_title=?, meta_description=?, meta_image=? WHERE id=?');
        $stmt->execute([
            $slug,
            trim($payload['shopee_url'] ?? ''),
            trim($payload['telegram_url'] ?? ''),
            trim($payload['meta_title'] ?? ''),
            trim($payload['meta_description'] ?? ''),
            $metaImage,
            $id,
        ]);
        return [true, 'Đã cập nhật link', $id];
    }

    $stmt = $pdo->prepare('INSERT INTO links (slug, shopee_url, telegram_url, meta_title, meta_description, meta_image) VALUES (?,?,?,?,?,?)');
    $stmt->execute([
        $slug,
        trim($payload['shopee_url'] ?? ''),
        trim($payload['telegram_url'] ?? ''),
        trim($payload['meta_title'] ?? ''),
        trim($payload['meta_description'] ?? ''),
        $metaImage,
    ]);
    return [true, 'Đã tạo link mới', (int)$pdo->lastInsertId()];
}

function save_upload(array $file): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $target = 'uploads/' . uniqid('meta_', true) . ($ext ? '.' . $ext : '');
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $target;
    }
    return null;
}

function fetch_stats(PDO $pdo): array
{
    $total = (int)$pdo->query('SELECT SUM(clicks) FROM links')->fetchColumn();
    $daily = (int)$pdo->query("SELECT COUNT(*) FROM clicks WHERE DATE(created_at)=CURDATE() AND is_bot=0")->fetchColumn();
    $monthly = (int)$pdo->query("SELECT COUNT(*) FROM clicks WHERE MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE()) AND is_bot=0")->fetchColumn();
    return ['total' => $total, 'daily' => $daily, 'monthly' => $monthly];
}

function log_click(PDO $pdo, array $config, array $link, string $userAgent, string $ip): void
{
    $isBot = is_bot_request($userAgent);
    [$browser, $os] = parse_user_agent($userAgent);
    $stmt = $pdo->prepare('INSERT INTO clicks (link_id, ip, user_agent, browser, os, is_bot) VALUES (?,?,?,?,?,?)');
    $stmt->execute([$link['id'], $ip, $userAgent, $browser, $os, $isBot ? 1 : 0]);
    if (!$isBot) {
        $pdo->prepare('UPDATE links SET clicks = clicks + 1 WHERE id = ?')->execute([$link['id']]);
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM clicks WHERE link_id = ? AND ip = ? AND is_bot = 0');
        $stmt->execute([$link['id'], $ip]);
        $times = (int)$stmt->fetchColumn();
        $message = "👤 Click mới\nIP: {$ip}\nLượt thứ: {$times}\nTrình duyệt: {$browser}\nHệ điều hành: {$os}\nLink: " . base_url() . '/' . $link['slug'];
        send_telegram($config, $message);
    }
}

function reset_link_clicks(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('UPDATE links SET clicks = 0 WHERE id = ?');
    $stmt->execute([$id]);
    $stmt = $pdo->prepare('DELETE FROM clicks WHERE link_id = ?');
    $stmt->execute([$id]);
}

function reset_all_clicks(PDO $pdo): void
{
    $pdo->exec('UPDATE links SET clicks = 0');
    $pdo->exec('DELETE FROM clicks');
}

function render_styles(string $primary, string $accent): string
{
    return file_get_contents(__DIR__ . '/static/css/style.css');
}
