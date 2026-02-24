<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/error_handler.php';

$pdo = db();
$settings = load_settings($pdo);
$contact = trim($settings['contact_link'] ?? '');
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang tin</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container home">
        <h1>Bảng tin</h1>
        <?php if ($contact): ?>
            <a class="primary-btn" href="<?php echo htmlspecialchars($contact, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Liên hệ admin để gửi thông tin mới</a>
        <?php else: ?>
            <p>Liên hệ admin để gửi thông tin mới.</p>
        <?php endif; ?>
    </div>
</body>
</html>
