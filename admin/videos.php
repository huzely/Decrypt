<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/partials.php';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM videos WHERE id = :id');
    $stmt->execute(['id' => $id]);
    header('Location: videos.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $type = $_POST['type'] === 'embed' ? 'embed' : 'mp4';
    $videoUrl = trim($_POST['video_url'] ?? '');
    $thumbnail = trim($_POST['thumbnail'] ?? '');
    $duration = trim($_POST['duration'] ?? '00:00');

    if (!empty($_FILES['mp4_file']['name']) && $_FILES['mp4_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['mp4_file']['name'], PATHINFO_EXTENSION));
        if ($ext === 'mp4') {
            $name = uniqid('video_', true) . '.mp4';
            $target = __DIR__ . '/../uploads/videos/' . $name;
            move_uploaded_file($_FILES['mp4_file']['tmp_name'], $target);
            $videoUrl = 'uploads/videos/' . $name;
        }
    }

    if (!empty($_FILES['thumbnail_file']['name']) && $_FILES['thumbnail_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['thumbnail_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $name = uniqid('thumb_', true) . '.' . $ext;
            $target = __DIR__ . '/../uploads/thumbnails/' . $name;
            move_uploaded_file($_FILES['thumbnail_file']['tmp_name'], $target);
            $thumbnail = 'uploads/thumbnails/' . $name;
        }
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE videos SET title=:title, description=:description, thumbnail=:thumbnail, video_url=:video_url, type=:type, duration=:duration WHERE id=:id');
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'thumbnail' => $thumbnail,
            'video_url' => $videoUrl,
            'type' => $type,
            'duration' => $duration,
            'id' => $id,
        ]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO videos (title, description, thumbnail, video_url, type, duration) VALUES (:title, :description, :thumbnail, :video_url, :type, :duration)');
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'thumbnail' => $thumbnail,
            'video_url' => $videoUrl,
            'type' => $type,
            'duration' => $duration,
        ]);
    }
    header('Location: videos.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM videos WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $edit = $stmt->fetch();
}

$videos = $pdo->query('SELECT * FROM videos ORDER BY id DESC')->fetchAll();
adminHeader('Videos');
?>
<h2>Quản lý Video</h2>
<div class="card card-dark p-3 mb-4">
<form method="post" enctype="multipart/form-data" class="row g-2">
    <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
    <div class="col-md-6"><input class="form-control" name="title" placeholder="Title" required value="<?= e($edit['title'] ?? '') ?>"></div>
    <div class="col-md-6"><input class="form-control" name="duration" placeholder="Duration (mm:ss)" value="<?= e($edit['duration'] ?? '00:00') ?>"></div>
    <div class="col-md-12"><textarea class="form-control" name="description" rows="2" placeholder="Description"><?= e($edit['description'] ?? '') ?></textarea></div>
    <div class="col-md-4"><select class="form-select" name="type"><option value="embed">Embed</option><option value="mp4" <?= (($edit['type'] ?? '') === 'mp4') ? 'selected' : '' ?>>MP4</option></select></div>
    <div class="col-md-4"><input class="form-control" name="video_url" placeholder="Embed URL / MP4 URL" value="<?= e($edit['video_url'] ?? '') ?>"></div>
    <div class="col-md-4"><input class="form-control" name="thumbnail" placeholder="Thumbnail URL" value="<?= e($edit['thumbnail'] ?? '') ?>"></div>
    <div class="col-md-6"><label class="small">Upload MP4</label><input class="form-control" type="file" name="mp4_file" accept="video/mp4"></div>
    <div class="col-md-6"><label class="small">Upload Thumbnail</label><input class="form-control" type="file" name="thumbnail_file" accept="image/*"></div>
    <div class="col-md-12"><button class="btn btn-danger">Lưu video</button></div>
</form></div>
<div class="table-responsive"><table class="table table-dark table-striped align-middle">
<thead><tr><th>ID</th><th>Title</th><th>Type</th><th>Views</th><th>Action</th></tr></thead><tbody>
<?php foreach ($videos as $v): ?><tr><td><?= (int) $v['id'] ?></td><td><?= e($v['title']) ?></td><td><?= e($v['type']) ?></td><td><?= number_format((int) $v['views']) ?></td>
<td><a class="btn btn-sm btn-warning" href="videos.php?edit=<?= (int) $v['id'] ?>">Edit</a> <a class="btn btn-sm btn-danger" onclick="return confirm('Delete?')" href="videos.php?delete=<?= (int) $v['id'] ?>">Delete</a></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php adminFooter(); ?>
