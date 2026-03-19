<?php
require_once __DIR__ . '/../includes/functions.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    save_video($_POST);
    $message = 'Video saved successfully.';
}
require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel form-panel">
    <h1>Upload Video</h1>
    <?php if ($message): ?><p class="success"><?php echo esc($message); ?></p><?php endif; ?>
    <form method="post" class="admin-form">
        <label>Title<input type="text" name="title" required></label>
        <label>Description<textarea name="description" rows="5"></textarea></label>
        <label>Thumbnail URL<input type="url" name="thumbnail" required></label>
        <label>Video URL<input type="text" name="video_url" required></label>
        <label>Type<select name="type"><option value="mp4">mp4</option><option value="embed">embed</option></select></label>
        <label>Category<input type="text" name="category" required></label>
        <label>Tags (comma separated)<input type="text" name="tags"></label>
        <button type="submit" class="btn-primary">Save Video</button>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
