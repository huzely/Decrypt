<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id'])) {
        $stmt = $pdo->prepare('UPDATE ads SET image=:image, link=:link, position=:position, active=:active WHERE id=:id');
        $stmt->execute(['image' => trim($_POST['image']), 'link' => trim($_POST['link']), 'position' => trim($_POST['position']), 'active' => !empty($_POST['active']) ? 1 : 0, 'id' => (int) $_POST['id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO ads (image, link, position, active) VALUES (:image, :link, :position, :active)');
        $stmt->execute(['image' => trim($_POST['image']), 'link' => trim($_POST['link']), 'position' => trim($_POST['position']), 'active' => !empty($_POST['active']) ? 1 : 0]);
    }
    header('Location: ads.php'); exit;
}
if (isset($_GET['delete'])) { $pdo->prepare('DELETE FROM ads WHERE id=:id')->execute(['id' => (int) $_GET['delete']]); header('Location: ads.php'); exit; }
$editing = null;
if (isset($_GET['edit'])) { $editing = $pdo->prepare('SELECT * FROM ads WHERE id=:id'); $editing->execute(['id' => (int) $_GET['edit']]); $editing = $editing->fetch(); }
$ads = get_ads(null, false);
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Ads</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="admin-layout"><?php include __DIR__ . '/partials/sidebar.php'; ?><main class="admin-main"><section class="panel"><h1>Ads</h1><form method="post" class="admin-form"><?php if ($editing): ?><input type="hidden" name="id" value="<?php echo (int) $editing['id']; ?>"><?php endif; ?><label>Image URL<input name="image" value="<?php echo esc($editing['image'] ?? ''); ?>" required></label><label>Link<input name="link" value="<?php echo esc($editing['link'] ?? ''); ?>" required></label><label>Position<select name="position"><?php foreach (['popup','header','middle','footer'] as $position): ?><option value="<?php echo $position; ?>" <?php echo (($editing['position'] ?? '') === $position) ? 'selected' : ''; ?>><?php echo ucfirst($position); ?></option><?php endforeach; ?></select></label><label class="inline-check"><input type="checkbox" name="active" value="1" <?php echo !isset($editing['active']) || !empty($editing['active']) ? 'checked' : ''; ?>> Active</label><button class="btn-primary" type="submit"><?php echo $editing ? 'Update' : 'Create'; ?> Ad</button></form></section><section class="panel"><table class="data-table"><tr><th>Position</th><th>Active</th><th>Actions</th></tr><?php foreach ($ads as $ad): ?><tr><td><?php echo esc($ad['position']); ?></td><td><?php echo $ad['active'] ? 'Yes' : 'No'; ?></td><td><a href="?edit=<?php echo (int) $ad['id']; ?>">Edit</a> | <a href="?delete=<?php echo (int) $ad['id']; ?>">Delete</a></td></tr><?php endforeach; ?></table></section></main></div></body></html>
