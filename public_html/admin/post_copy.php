<?php
require_once __DIR__ . '/../lib/error_handler.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_auth();

$slug = $_GET['slug'] ?? '';
$link = $slug ? current_url($slug) : current_url();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Copy link</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body style="display:grid; place-items:center; min-height:100vh;">
<div class="card" style="width:min(420px, 92%); text-align:center;">
    <p>Link bài viết:</p>
    <code style="display:block; margin-bottom:12px; word-break:break-all;"><?php echo e($link); ?></code>
    <button class="btn" id="copy">Copy</button>
    <p style="margin-top:12px; color:#94a3b8;">Dán link gửi cho bạn đọc.</p>
</div>
<script>
document.getElementById('copy').addEventListener('click', function(){
    navigator.clipboard.writeText('<?php echo e($link); ?>').then(() => alert('Đã copy link!'));
});
</script>
</body>
</html>
