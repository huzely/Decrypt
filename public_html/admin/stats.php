<?php
require __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $pdo->exec('TRUNCATE TABLE click_events');
    $pdo->exec('TRUNCATE TABLE admin_logs');
    cache_clear($config);
}
$logs = $pdo->query('SELECT * FROM click_events ORDER BY created_at DESC LIMIT 100')->fetchAll();
?>
<h1>Thống kê</h1>
<form method="post" onsubmit="return confirm('Reset thống kê?');">
    <button class="btn btn-secondary" name="reset" value="1" type="submit">Reset thống kê</button>
</form>
<table class="table">
    <thead><tr><th>Thời gian</th><th>Event</th><th>Slug</th><th>IP hash</th><th>UA hash</th></tr></thead>
    <tbody>
    <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= htmlspecialchars($log['created_at'], ENT_QUOTES); ?></td>
            <td><?= htmlspecialchars($log['event_type'], ENT_QUOTES); ?></td>
            <td><?= htmlspecialchars($log['slug'], ENT_QUOTES); ?></td>
            <td><?= htmlspecialchars($log['ip_hash'], ENT_QUOTES); ?></td>
            <td><?= htmlspecialchars($log['ua_hash'], ENT_QUOTES); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php require __DIR__ . '/includes/footer.php'; ?>
