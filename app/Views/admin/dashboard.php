<?php $settings = $settings ?? []; include __DIR__ . '/layout_top.php'; ?>
<section class="cards">
    <div class="card">Pageviews: <?= $stats['views'] ?? 0 ?></div>
    <div class="card">Click Shopee: <?= $stats['clicks'] ?? 0 ?></div>
    <div class="card">CTR: <?= ($stats['views'] ?? 0) ? round(($stats['clicks'] / $stats['views']) * 100, 2) : 0 ?>%</div>
</section>
<section>
    <h3>Thống kê theo ngày</h3>
    <table>
        <tr><th>Ngày</th><th>View</th><th>Click</th></tr>
        <?php foreach ($daily as $row): ?>
            <tr><td><?= $row['date'] ?></td><td><?= $row['views'] ?></td><td><?= $row['clicks'] ?></td></tr>
        <?php endforeach; ?>
    </table>
</section>
<section>
    <h3>Theo bài</h3>
    <table>
        <tr><th>ID</th><th>Tiêu đề</th><th>View</th><th>Click</th></tr>
        <?php foreach ($byArticle as $row): ?>
            <tr><td><?= $row['id'] ?></td><td><?= App\Core\Security::escape($row['title']) ?></td><td><?= $row['views'] ?? 0 ?></td><td><?= $row['clicks'] ?? 0 ?></td></tr>
        <?php endforeach; ?>
    </table>
</section>
<section>
    <h3>Nhật ký reset</h3>
    <ul>
        <?php foreach (($settings['reset_logs'] ?? []) as $log): ?>
            <li><?= App\Core\Security::escape($log['by']) ?> lúc <?= $log['at'] ?></li>
        <?php endforeach; ?>
    </ul>
</section>
<form method="post" action="<?= base_url('admin/reset-stats') ?>" onsubmit="return confirm('Reset thống kê?')">
    <?= csrf_field() ?>
    <button type="submit">Reset thống kê</button>
</form>
<a href="<?= base_url('admin/export') ?>" class="btn">Export CSV</a>
<?php include __DIR__ . '/layout_bottom.php'; ?>
