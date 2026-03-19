<?php
require_once __DIR__ . '/../includes/functions.php';
if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (admin_login(trim($_POST['username']), $_POST['password'])) {
        header('Location: index.php');
        exit;
    }
    $error = 'Invalid login credentials.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="login-shell">
    <form method="post" class="panel login-card admin-form">
        <h1>Admin Login</h1>
        <?php if ($error): ?><p class="error"><?php echo esc($error); ?></p><?php endif; ?>
        <label>Username<input type="text" name="username" required></label>
        <label>Password<input type="password" name="password" required></label>
        <button type="submit" class="btn-primary">Sign In</button>
        <p class="muted">Default admin is created in <code>database.sql</code>.</p>
    </form>
</div>
</body>
</html>
