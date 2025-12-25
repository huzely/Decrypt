<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../app/lib/db.php';
require_once __DIR__ . '/../app/lib/csrf.php';
$settings = db()->query('SELECT * FROM site_settings LIMIT 1')->fetch();
if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_verify();
    $logoPath = $settings['logo_path'];
    $bannerPath = $settings['banner_path'];
    if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['logo']['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/png'=>'png','image/jpeg'=>'jpg','image/webp'=>'webp'];
        if (isset($allowed[$mime]) && $_FILES['logo']['size'] <= 2*1024*1024) {
            $ext = $allowed[$mime];
            $name = 'assets/img/logo_'.time().'.'.$ext;
            move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__.'/../'.$name);
            $logoPath = $name;
        }
    }
    if (!empty($_FILES['banner']['name']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['banner']['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/png'=>'png','image/jpeg'=>'jpg','image/webp'=>'webp'];
        if (isset($allowed[$mime]) && $_FILES['banner']['size'] <= 3*1024*1024) {
            $ext = $allowed[$mime];
            $name = 'assets/img/banner_'.time().'.'.$ext;
            move_uploaded_file($_FILES['banner']['tmp_name'], __DIR__.'/../'.$name);
            $bannerPath = $name;
        }
    }
    $upd = db()->prepare('UPDATE site_settings SET site_name=:sn, site_description=:sd, logo_path=:logo, banner_path=:banner, theme=:theme, ads_enabled=:ads, ad_link=:link, ad_title=:title, ad_body=:body, updated_at=NOW() WHERE id=1');
    $upd->execute([
        ':sn'=>$_POST['site_name'], ':sd'=>$_POST['site_description'], ':logo'=>$logoPath, ':banner'=>$bannerPath,
        ':theme'=>$_POST['theme'], ':ads'=>!empty($_POST['ads_enabled'])?1:0, ':link'=>$_POST['ad_link'], ':title'=>$_POST['ad_title'], ':body'=>$_POST['ad_body']
    ]);
    header('Location: '.BASE_URL.'/admin/settings.php');
    exit;
}
include __DIR__ . '/includes/header.php';
?>
<h3>Cài đặt</h3>
<form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <label>Tên site<input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>"></label>
    <label>Mô tả<input type="text" name="site_description" value="<?= htmlspecialchars($settings['site_description']) ?>"></label>
    <label>Logo<input type="file" name="logo"></label>
    <label>Banner<input type="file" name="banner"></label>
    <label>Theme<select name="theme">
        <option value="1" <?= $settings['theme']==='1'?'selected':'' ?>>Theme 1</option>
        <option value="2" <?= $settings['theme']==='2'?'selected':'' ?>>Theme 2</option>
        <option value="3" <?= $settings['theme']==='3'?'selected':'' ?>>Theme 3</option>
    </select></label>
    <label><input type="checkbox" name="ads_enabled" value="1" <?= $settings['ads_enabled']?'checked':'' ?>> Bật quảng cáo</label>
    <label>Link Shopee<input type="url" name="ad_link" value="<?= htmlspecialchars($settings['ad_link']) ?>"></label>
    <label>Tiêu đề quảng cáo<input type="text" name="ad_title" value="<?= htmlspecialchars($settings['ad_title']) ?>"></label>
    <label>Nội dung quảng cáo<textarea name="ad_body"><?= htmlspecialchars($settings['ad_body']) ?></textarea></label>
    <button class="btn" type="submit">Lưu</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
