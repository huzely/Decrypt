<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/partials.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = trim($_POST['image'] ?? '');
    $link1 = trim($_POST['link1'] ?? '');
    $link2 = trim($_POST['link2'] ?? '');
    $links = implode(',', array_filter([$link1, $link2]));

    $pdo->exec("DELETE FROM ads WHERE type='popup'");
    $stmt = $pdo->prepare("INSERT INTO ads (type,image,link,status) VALUES ('popup',:image,:link,1)");
    $stmt->execute(['image' => $image, 'link' => $links]);
    header('Location: popup.php?saved=1');
    exit;
}

$popup = $pdo->query("SELECT * FROM ads WHERE type='popup' ORDER BY id DESC LIMIT 1")->fetch();
$links = array_values(array_filter(array_map('trim', explode(',', (string) ($popup['link'] ?? '')))));
adminHeader('Popup Ads');
?>
<h2>Popup Ads</h2>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Saved</div><?php endif; ?>
<div class="card card-dark p-3">
<form method="post" class="row g-3">
<div class="col-md-12"><label>Popup image URL</label><input name="image" class="form-control" required value="<?= e($popup['image'] ?? '') ?>"></div>
<div class="col-md-6"><label>Link quảng cáo 1</label><input name="link1" class="form-control" required value="<?= e($links[0] ?? '') ?>"></div>
<div class="col-md-6"><label>Link quảng cáo 2 (optional)</label><input name="link2" class="form-control" value="<?= e($links[1] ?? '') ?>"></div>
<div class="col-12"><button class="btn btn-danger">Lưu popup</button></div>
</form>
</div>
<?php adminFooter(); ?>
