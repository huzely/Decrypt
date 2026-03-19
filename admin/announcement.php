<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $pdo->query('SELECT id FROM announcements ORDER BY id DESC LIMIT 1')->fetchColumn();
    if ($current) {
        $stmt = $pdo->prepare('UPDATE announcements SET title=:title, content=:content, active=:active WHERE id=:id');
        $stmt->execute(['title' => trim($_POST['title']), 'content' => trim($_POST['content']), 'active' => !empty($_POST['active']) ? 1 : 0, 'id' => $current]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO announcements (title, content, active) VALUES (:title, :content, :active)');
        $stmt->execute(['title' => trim($_POST['title']), 'content' => trim($_POST['content']), 'active' => !empty($_POST['active']) ? 1 : 0]);
    }
    header('Location: announcement.php'); exit;
}
$announcement = $pdo->query('SELECT * FROM announcements ORDER BY id DESC LIMIT 1')->fetch();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Announcement</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="admin-layout"><?php include __DIR__ . '/partials/sidebar.php'; ?><main class="admin-main"><section class="panel"><h1>Announcement</h1><form method="post" class="admin-form"><label>Title<input name="title" value="<?php echo esc($announcement['title'] ?? ''); ?>" required></label><label>Content<textarea name="content" rows="5" required><?php echo esc($announcement['content'] ?? ''); ?></textarea></label><label class="inline-check"><input type="checkbox" name="active" value="1" <?php echo !isset($announcement['active']) || !empty($announcement['active']) ? 'checked' : ''; ?>> Active</label><button class="btn-primary" type="submit">Save Announcement</button></form></section></main></div></body></html>
