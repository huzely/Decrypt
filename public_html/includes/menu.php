<div class="hamburger" aria-label="Menu">
    <span></span><span></span><span></span>
</div>
<nav class="mobile-menu">
    <div class="panel">
        <h3><?php echo e($settings['site_name'] ?? 'Tin nhanh'); ?></h3>
        <p style="color:var(--muted);"><?php echo e($settings['site_description'] ?? ''); ?></p>
        <div class="grid">
            <a class="btn" href="/">Trang chủ</a>
            <a class="btn" href="/admin/login.php">Đăng nhập</a>
        </div>
    </div>
</nav>
