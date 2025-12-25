<?php
if (file_exists(__DIR__ . '/config.php') && !isset($_GET['force'])) {
    echo 'config.php đã tồn tại. Xóa hoặc thêm ?force=1 để cài lại.';
    exit;
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUser = $_POST['db_user'];
    $dbPass = $_POST['db_pass'];
    $adminEmail = $_POST['admin_email'];
    $adminPass = $_POST['admin_pass'];
    $siteName = $_POST['site_name'];
    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $schema = file_get_contents(__DIR__ . '/database/schema.sql');
        $pdo->exec($schema);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :pass)');
        $stmt->execute([':name' => 'Admin', ':email' => $adminEmail, ':pass' => password_hash($adminPass, PASSWORD_DEFAULT)]);
        $settingStmt = $pdo->prepare('INSERT INTO settings (`key`, `value`) VALUES (:k, :v) ON DUPLICATE KEY UPDATE value = VALUES(value)');
        $settingStmt->execute([':k' => 'site_name', ':v' => json_encode($siteName) ]);
        $config = [
            'db' => [ 'host' => $dbHost, 'name' => $dbName, 'user' => $dbUser, 'pass' => $dbPass, 'charset' => 'utf8mb4'],
            'app' => [ 'base_url' => rtrim($_POST['base_url'], '/'), 'environment' => 'production', 'debug' => false, 'session_name' => 'decrypt_news_session' ],
            'security' => [ 'csrf_key' => bin2hex(random_bytes(16)) ],
        ];
        file_put_contents(__DIR__ . '/config.php', '<?php return ' . var_export($config, true) . ';');
        $message = 'Cài đặt thành công. Xóa file setup.php sau khi hoàn tất.';
    } catch (Exception $e) {
        $message = 'Lỗi: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Setup Wizard</title>
<style>body{font-family:Arial;background:#f3f4f6;padding:30px;}form{background:#fff;padding:20px;border-radius:12px;max-width:480px;margin:auto;box-shadow:0 2px 10px rgba(0,0,0,0.1);}label{display:block;margin-bottom:10px;}input{width:100%;padding:10px;margin-top:4px;border-radius:8px;border:1px solid #e5e7eb;}button{padding:12px 16px;border:none;border-radius:8px;background:#2563eb;color:#fff;cursor:pointer;width:100%;} .msg{margin:10px 0;color:#2563eb;}</style>
</head>
<body>
<h1>Cài đặt</h1>
<?php if ($message): ?><div class="msg"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<form method="post">
    <label>Base URL<input type="text" name="base_url" required placeholder="https://example.com"></label>
    <label>DB Host<input type="text" name="db_host" required></label>
    <label>DB Name<input type="text" name="db_name" required></label>
    <label>DB User<input type="text" name="db_user" required></label>
    <label>DB Password<input type="password" name="db_pass"></label>
    <label>Tên trang<input type="text" name="site_name" required value="Tin tức"></label>
    <label>Admin Email<input type="email" name="admin_email" required></label>
    <label>Admin Password<input type="password" name="admin_pass" required></label>
    <button type="submit">Cài đặt</button>
</form>
</body>
</html>
