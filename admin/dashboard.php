<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/partials.php';

$totalVideos = (int) $pdo->query('SELECT COUNT(*) FROM videos')->fetchColumn();
$totalViews = (int) $pdo->query('SELECT COALESCE(SUM(views),0) FROM videos')->fetchColumn();
$todayViews = (int) $pdo->query('SELECT COALESCE(SUM(daily_views),0) FROM video_stats WHERE view_date = CURDATE()')->fetchColumn();
$topVideo = $pdo->query('SELECT title, views FROM videos ORDER BY views DESC LIMIT 1')->fetch();

$chartRows = $pdo->query('SELECT view_date, COALESCE(SUM(daily_views),0) AS views FROM video_stats GROUP BY view_date ORDER BY view_date DESC LIMIT 7')->fetchAll();
$labels = [];
$values = [];
foreach (array_reverse($chartRows) as $row) {
    $labels[] = $row['view_date'];
    $values[] = (int) $row['views'];
}

adminHeader('Dashboard');
?>
<h2>Dashboard</h2>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card card-dark p-3"><small>Total Video</small><h3><?= $totalVideos ?></h3></div></div>
    <div class="col-md-3"><div class="card card-dark p-3"><small>Total Views</small><h3><?= number_format($totalViews) ?></h3></div></div>
    <div class="col-md-3"><div class="card card-dark p-3"><small>Views Today</small><h3><?= number_format($todayViews) ?></h3></div></div>
    <div class="col-md-3"><div class="card card-dark p-3"><small>Top Video</small><h6 class="mb-0"><?= e($topVideo['title'] ?? 'N/A') ?></h6></div></div>
</div>
<div class="card card-dark p-3">
    <h5>Thống kê theo ngày</h5>
    <canvas id="viewsChart" height="100"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('viewsChart'), {
    type: 'line',
    data: { labels: <?= json_encode($labels) ?>, datasets: [{label: 'Views', data: <?= json_encode($values) ?>, borderColor: '#c00', tension: 0.4}]},
    options: { plugins: {legend: {labels:{color:'#ddd'}}}, scales: {x:{ticks:{color:'#aaa'}}, y:{ticks:{color:'#aaa'}}}}
});
</script>
<?php adminFooter(); ?>
