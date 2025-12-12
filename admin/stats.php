<?php include __DIR__ . '/includes/header.php'; ?>
<?php
$day = $_GET['date'] ?? date('Y-m-d');
$stmt = $pdo->prepare('SELECT cl.*, p.title FROM click_logs cl LEFT JOIN posts p ON p.id = cl.post_id WHERE DATE(cl.clicked_at)=:d ORDER BY cl.clicked_at DESC LIMIT 200');
$stmt->execute([':d' => $day]);
$logs = $stmt->fetchAll();
?>
<h1>Log click Shopee</h1>
<form method="get" style="margin-bottom:10px;">
    <input type="date" name="date" value="<?= escape_html($day); ?>">
    <button class="btn primary">Lọc</button>
</form>
<table>
    <thead><tr><th>Thời gian</th><th>Post</th><th>IP</th><th>User Agent</th><th>Valid</th><th>Lý do</th></tr></thead>
    <tbody>
    <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= escape_html($log['clicked_at']); ?></td>
            <td>#<?= (int)$log['post_id']; ?> - <?= escape_html($log['title']); ?></td>
            <td><?= escape_html($log['ip_address']); ?></td>
            <td><?= escape_html($log['user_agent']); ?></td>
            <td><?= $log['is_valid'] ? '✅' : '❌'; ?></td>
            <td><?= escape_html($log['reason']); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/includes/footer.php'; ?>
