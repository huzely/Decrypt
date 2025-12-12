<?php include __DIR__ . '/includes/header.php'; ?>
<?php
$cssPath = __DIR__ . '/../assets/css/custom.css';
$cssContent = file_exists($cssPath) ? file_get_contents($cssPath) : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newCss = $_POST['css'] ?? '';
    file_put_contents($cssPath, $newCss);
    redirect_with_message('/admin/theme.php','Đã lưu CSS');
}
?>
<h1>Tuỳ chỉnh giao diện</h1>
<form method="post">
    <div class="form-group"><label>Custom CSS</label><textarea name="css" rows="12"><?= escape_html($cssContent); ?></textarea></div>
    <button class="btn primary" type="submit">Lưu</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
