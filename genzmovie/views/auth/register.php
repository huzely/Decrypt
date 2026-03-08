<?php $metaTitle='Đăng ký'; include __DIR__ . '/../layouts/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-5">
<h3>Đăng ký</h3>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="POST"><?= csrf_field() ?>
    <input class="form-control mb-2" type="text" name="name" placeholder="Họ tên" required>
    <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
    <input class="form-control mb-2" type="password" name="password" placeholder="Mật khẩu" required>
    <button class="btn btn-danger w-100">Đăng ký</button>
</form></div></div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
