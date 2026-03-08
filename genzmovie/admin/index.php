<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_admin();

$pdo = Database::connection();
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validate_csrf($_POST['csrf_token'] ?? null)) {
    if (($_POST['action'] ?? '') === 'import_ophim') {
        include __DIR__ . '/../crawler/ophim_crawler.php';
        $message = 'Đã chạy crawler OPhim.';
    }
    if (($_POST['action'] ?? '') === 'add_ad') {
        $stmt = $pdo->prepare('INSERT INTO ads (name, position, ad_type, ad_code, status, created_at, updated_at) VALUES (:name,:position,:ad_type,:ad_code,1,NOW(),NOW())');
        $stmt->execute([
            'name' => $_POST['name'],
            'position' => $_POST['position'],
            'ad_type' => $_POST['ad_type'],
            'ad_code' => $_POST['ad_code'],
        ]);
        $message = 'Đã thêm quảng cáo';
    }
}

$movies = $pdo->query('SELECT * FROM movies ORDER BY id DESC LIMIT 20')->fetchAll();
$users = $pdo->query('SELECT id,name,email,status FROM users ORDER BY id DESC LIMIT 20')->fetchAll();
$comments = $pdo->query('SELECT c.id,c.content,m.title FROM comments c JOIN movies m ON m.id=c.movie_id ORDER BY c.id DESC LIMIT 20')->fetchAll();
$ads = $pdo->query('SELECT * FROM ads ORDER BY id DESC')->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin - GENZMOVIE</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"></head>
<body><div class="container py-4">
<h2>Admin Dashboard</h2>
<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
<div class="row g-3">
<div class="col-md-6"><div class="card"><div class="card-body"><h5>Crawler Control</h5><form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="import_ophim"><button class="btn btn-primary">Import từ OPhim</button></form><p class="mt-2 small">Cronjob: <code>*/30 * * * * php /path/genzmovie/crawler/ophim_crawler.php</code></p></div></div></div>
<div class="col-md-6"><div class="card"><div class="card-body"><h5>Ads Management</h5><form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="add_ad"><input class="form-control mb-2" name="name" placeholder="Tên quảng cáo" required><select class="form-select mb-2" name="position"><option>header</option><option>sidebar</option><option>popup</option><option>video_preroll</option><option>footer</option></select><select class="form-select mb-2" name="ad_type"><option>adsense</option><option>custom_html</option><option>script</option></select><textarea class="form-control mb-2" name="ad_code" rows="3" required></textarea><button class="btn btn-danger">Lưu quảng cáo</button></form></div></div></div>
</div>
<hr>
<h5>Movie Management</h5><table class="table table-striped"><tr><th>ID</th><th>Title</th><th>Year</th></tr><?php foreach($movies as $m): ?><tr><td><?= $m['id'] ?></td><td><?= e($m['title']) ?></td><td><?= e((string)$m['year']) ?></td></tr><?php endforeach; ?></table>
<h5>User Management</h5><table class="table table-striped"><tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th></tr><?php foreach($users as $u): ?><tr><td><?= $u['id'] ?></td><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['status']) ?></td></tr><?php endforeach; ?></table>
<h5>Comment Management</h5><table class="table table-striped"><tr><th>ID</th><th>Movie</th><th>Comment</th></tr><?php foreach($comments as $c): ?><tr><td><?= $c['id'] ?></td><td><?= e($c['title']) ?></td><td><?= e($c['content']) ?></td></tr><?php endforeach; ?></table>
<h5>Ads</h5><table class="table table-striped"><tr><th>Name</th><th>Position</th><th>Type</th></tr><?php foreach($ads as $ad): ?><tr><td><?= e($ad['name']) ?></td><td><?= e($ad['position']) ?></td><td><?= e($ad['ad_type']) ?></td></tr><?php endforeach; ?></table>
</div></body></html>
