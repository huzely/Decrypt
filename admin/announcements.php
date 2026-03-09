<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/partials.php';

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM announcements WHERE id=:id')->execute(['id' => (int) $_GET['delete']]);
    header('Location: announcements.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title !== '' && $content !== '') {
        $pdo->prepare('INSERT INTO announcements (title, content) VALUES (:title, :content)')->execute(compact('title', 'content'));
    }
    header('Location: announcements.php');
    exit;
}
$rows = $pdo->query('SELECT * FROM announcements ORDER BY id DESC')->fetchAll();
adminHeader('Announcements');
?>
<h2>Announcements</h2>
<div class="card card-dark p-3 mb-3"><form method="post" class="row g-2"><div class="col-md-4"><input class="form-control" name="title" required placeholder="Title"></div><div class="col-md-6"><input class="form-control" name="content" required placeholder="Content"></div><div class="col-md-2"><button class="btn btn-danger w-100">Add</button></div></form></div>
<table class="table table-dark"><thead><tr><th>ID</th><th>Title</th><th>Content</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= (int)$r['id'] ?></td><td><?= e($r['title']) ?></td><td><?= e($r['content']) ?></td><td><a class="btn btn-sm btn-danger" href="announcements.php?delete=<?= (int)$r['id'] ?>">Delete</a></td></tr><?php endforeach; ?></tbody></table>
<?php adminFooter(); ?>
