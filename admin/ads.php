<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/partials.php';

if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $pdo->prepare('UPDATE ads SET status = IF(status=1,0,1) WHERE id=:id')->execute(['id' => $id]);
    header('Location: ads.php');
    exit;
}
if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM ads WHERE id=:id')->execute(['id' => (int) $_GET['delete']]);
    header('Location: ads.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = in_array($_POST['type'] ?? '', ['banner', 'google'], true) ? $_POST['type'] : 'banner';
    $image = trim($_POST['image'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $pdo->prepare('INSERT INTO ads (type,image,link,status) VALUES (:type,:image,:link,1)')->execute(compact('type', 'image', 'link'));
    header('Location: ads.php');
    exit;
}
$ads = $pdo->query("SELECT * FROM ads WHERE type IN ('banner','google') ORDER BY id DESC")->fetchAll();
adminHeader('Ads');
?>
<h2>Ads</h2>
<div class="card card-dark p-3 mb-4"><form method="post" class="row g-2">
<div class="col-md-3"><select name="type" class="form-select"><option value="banner">Banner</option><option value="google">Google Code</option></select></div>
<div class="col-md-4"><input name="image" class="form-control" placeholder="Image URL hoặc mã Google Ads"></div>
<div class="col-md-4"><input name="link" class="form-control" placeholder="Link redirect (banner)"></div>
<div class="col-md-1"><button class="btn btn-danger w-100">Add</button></div>
</form></div>
<table class="table table-dark table-striped"><thead><tr><th>ID</th><th>Type</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach ($ads as $ad): ?><tr><td><?= (int) $ad['id'] ?></td><td><?= e($ad['type']) ?></td><td><?= $ad['status'] ? 'ON' : 'OFF' ?></td><td><a href="ads.php?toggle=<?= (int) $ad['id'] ?>" class="btn btn-sm btn-warning">Toggle</a> <a href="ads.php?delete=<?= (int) $ad['id'] ?>" class="btn btn-sm btn-danger">Delete</a></td></tr><?php endforeach; ?>
</tbody></table>
<?php adminFooter(); ?>
