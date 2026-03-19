<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM videos WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['delete']]);
    header('Location: videos.php');
    exit;
}
$editing = null;
if (isset($_GET['edit'])) {
    $editing = fetch_video((int) $_GET['edit']);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
    save_video($_POST, $id);
    header('Location: videos.php');
    exit;
}
$videos = fetch_videos();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Manage Videos</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="admin-layout"><?php include __DIR__ . '/partials/sidebar.php'; ?><main class="admin-main"><section class="panel"><h1>Video Management</h1><form method="post" class="admin-form"><?php if ($editing): ?><input type="hidden" name="id" value="<?php echo (int) $editing['id']; ?>"><?php endif; ?><label>Title<input name="title" value="<?php echo esc($editing['title'] ?? ''); ?>" required></label><label>Description<textarea name="description" rows="4"><?php echo esc($editing['description'] ?? ''); ?></textarea></label><label>Thumbnail URL<input name="thumbnail" value="<?php echo esc($editing['thumbnail'] ?? ''); ?>" required></label><label>Video URL<input name="video_url" value="<?php echo esc($editing['video_url'] ?? ''); ?>" required></label><label>Type<select name="type"><option value="mp4" <?php echo (($editing['type'] ?? '') === 'mp4') ? 'selected' : ''; ?>>mp4</option><option value="embed" <?php echo (($editing['type'] ?? '') === 'embed') ? 'selected' : ''; ?>>embed</option></select></label><label>Category<input name="category" value="<?php echo esc($editing['category'] ?? ''); ?>" required></label><label>Tags<input name="tags" value="<?php echo esc($editing['tags'] ?? ''); ?>"></label><button type="submit" class="btn-primary"><?php echo $editing ? 'Update' : 'Create'; ?> Video</button></form></section><section class="panel"><table class="data-table"><tr><th>Title</th><th>Category</th><th>Views</th><th>Actions</th></tr><?php foreach ($videos as $video): ?><tr><td><?php echo esc($video['title']); ?></td><td><?php echo esc($video['category']); ?></td><td><?php echo (int) $video['views']; ?></td><td><a href="?edit=<?php echo (int) $video['id']; ?>">Edit</a> | <a href="?delete=<?php echo (int) $video['id']; ?>" onclick="return confirm('Delete this video?')">Delete</a></td></tr><?php endforeach; ?></table></section></main></div></body></html>
