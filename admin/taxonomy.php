<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$editCategory = null;
$editTag = null;
if (isset($_GET['edit_category'])) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id=:id');
    $stmt->execute(['id' => (int) $_GET['edit_category']]);
    $editCategory = $stmt->fetch();
}
if (isset($_GET['edit_tag'])) {
    $stmt = $pdo->prepare('SELECT * FROM tags WHERE id=:id');
    $stmt->execute(['id' => (int) $_GET['edit_tag']]);
    $editTag = $stmt->fetch();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['entity'] === 'category') {
        if (!empty($_POST['id'])) {
            $stmt = $pdo->prepare('UPDATE categories SET name=:name, description=:description WHERE id=:id');
            $stmt->execute(['name' => trim($_POST['name']), 'description' => trim($_POST['description']), 'id' => (int) $_POST['id']]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO categories (name, description) VALUES (:name, :description)');
            $stmt->execute(['name' => trim($_POST['name']), 'description' => trim($_POST['description'])]);
        }
    }
    if ($_POST['entity'] === 'tag') {
        if (!empty($_POST['id'])) {
            $stmt = $pdo->prepare('UPDATE tags SET name=:name WHERE id=:id');
            $stmt->execute(['name' => trim($_POST['name']), 'id' => (int) $_POST['id']]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO tags (name) VALUES (:name)');
            $stmt->execute(['name' => trim($_POST['name'])]);
        }
    }
    header('Location: taxonomy.php');
    exit;
}
if (isset($_GET['delete_category'])) { $pdo->prepare('DELETE FROM categories WHERE id=:id')->execute(['id' => (int) $_GET['delete_category']]); header('Location: taxonomy.php'); exit; }
if (isset($_GET['delete_tag'])) { $pdo->prepare('DELETE FROM tags WHERE id=:id')->execute(['id' => (int) $_GET['delete_tag']]); header('Location: taxonomy.php'); exit; }
$categories = get_categories();
$tags = get_tags();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Taxonomy</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="admin-layout"><?php include __DIR__ . '/partials/sidebar.php'; ?><main class="admin-main"><section class="panel split-panels"><div><h1>Categories</h1><form method="post" class="admin-form"><input type="hidden" name="entity" value="category"><?php if ($editCategory): ?><input type="hidden" name="id" value="<?php echo (int) $editCategory['id']; ?>"><?php endif; ?><label>Name<input name="name" value="<?php echo esc($editCategory['name'] ?? ''); ?>" required></label><label>Description<textarea name="description" rows="3"><?php echo esc($editCategory['description'] ?? ''); ?></textarea></label><button class="btn-primary" type="submit"><?php echo $editCategory ? 'Update' : 'Save'; ?> Category</button></form><table class="data-table"><tr><th>Name</th><th>Description</th><th>Action</th></tr><?php foreach ($categories as $category): ?><tr><td><?php echo esc($category['name']); ?></td><td><?php echo esc($category['description']); ?></td><td><a href="?edit_category=<?php echo (int) $category['id']; ?>">Edit</a> | <a href="?delete_category=<?php echo (int) $category['id']; ?>">Delete</a></td></tr><?php endforeach; ?></table></div><div><h1>Tags</h1><form method="post" class="admin-form"><input type="hidden" name="entity" value="tag"><?php if ($editTag): ?><input type="hidden" name="id" value="<?php echo (int) $editTag['id']; ?>"><?php endif; ?><label>Name<input name="name" value="<?php echo esc($editTag['name'] ?? ''); ?>" required></label><button class="btn-primary" type="submit"><?php echo $editTag ? 'Update' : 'Save'; ?> Tag</button></form><table class="data-table"><tr><th>Name</th><th>Action</th></tr><?php foreach ($tags as $tag): ?><tr><td><?php echo esc($tag['name']); ?></td><td><a href="?edit_tag=<?php echo (int) $tag['id']; ?>">Edit</a> | <a href="?delete_tag=<?php echo (int) $tag['id']; ?>">Delete</a></td></tr><?php endforeach; ?></table></div></section></main></div></body></html>
