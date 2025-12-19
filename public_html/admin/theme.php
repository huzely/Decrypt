<?php
require __DIR__ . '/includes/header.php';
$file = __DIR__ . '/../assets/css/custom.css';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents($file, $_POST['css'] ?? '');
    $msg = 'Đã lưu CSS';
}
$css = file_exists($file) ? file_get_contents($file) : '';
?>
<h1>Tuỳ chỉnh giao diện</h1>
<?php if ($msg): ?><p style="color:green;"><?= htmlspecialchars($msg, ENT_QUOTES); ?></p><?php endif; ?>
<form method="post">
    <div class="form-group">
        <label>CSS</label>
        <textarea name="css" rows="12"><?= htmlspecialchars($css, ENT_QUOTES); ?></textarea>
    </div>
    <button class="btn btn-primary" type="submit">Lưu</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
