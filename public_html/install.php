<?php
// Simple installer to generate config.php and import schema.
$configPath = __DIR__ . '/app/config/config.php';
$schemaPath = __DIR__ . '/sql/database.sql';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = trim($_POST['db_host'] ?? 'localhost');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = trim($_POST['db_pass'] ?? '');
    $baseUrl = trim($_POST['base_url'] ?? '');
    $salt = bin2hex(random_bytes(16));

    $configContent = "<?php\nreturn [\n    'DB_HOST' => '" . addslashes($dbHost) . "',\n    'DB_NAME' => '" . addslashes($dbName) . "',\n    'DB_USER' => '" . addslashes($dbUser) . "',\n    'DB_PASS' => '" . addslashes($dbPass) . "',\n    'BASE_URL' => '" . addslashes($baseUrl) . "',\n    'SESSION_SALT' => '" . $salt . "',\n    'TELEGRAM_BOT_TOKEN' => '',\n    'TELEGRAM_CHAT_ID' => '',\n    'TELEGRAM_ADMIN_IDS' => [],\n    'CACHE_PATH' => __DIR__ . '/../cache',\n    'CACHE_TTL' => [\n        'settings' => 300,\n        'home' => 45,\n        'article' => 45,\n    ],\n];\n";
    file_put_contents($configPath, $configContent);

    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $sql = file_get_contents($schemaPath);
        $pdo->exec($sql);
        $message = 'Đã tạo cấu hình và import database thành công. Xóa file install.php để bảo mật.';
    } catch (Throwable $e) {
        $message = 'Lỗi: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin auth">
    <div class="auth-card">
        <h2>Cài đặt hệ thống</h2>
        <?php if ($message): ?><p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
        <form method="post">
            <label>DB Host</label>
            <input type="text" name="db_host" required value="localhost">
            <label>DB Name</label>
            <input type="text" name="db_name" required>
            <label>DB User</label>
            <input type="text" name="db_user" required>
            <label>DB Pass</label>
            <input type="password" name="db_pass">
            <label>Base URL</label>
            <input type="text" name="base_url" value="http://your-domain.com">
            <button class="btn" type="submit">Cài đặt</button>
        </form>
    </div>
</body>
</html>
