<?php include __DIR__ . '/includes/header.php'; ?>
<?php
$totalPosts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$publishedPosts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
$today = date('Y-m-d');
$pageviewsToday = $pdo->prepare('SELECT pageviews, unique_visitors FROM page_views WHERE visit_date = :d');
$pageviewsToday->execute([':d' => $today]);
$pvRow = $pageviewsToday->fetch() ?: ['pageviews' => 0, 'unique_visitors' => 0];
$clicksToday = $pdo->prepare("SELECT SUM(is_valid) FROM click_logs WHERE DATE(clicked_at)=:d");
$clicksToday->execute([':d' => $today]);
$clicksTotal = $pdo->query('SELECT SUM(is_valid) FROM click_logs')->fetchColumn();
$clicksTodayVal = (int)($clicksToday->fetchColumn());
$clicksTotal = (int)$clicksTotal;
$chartData = recent_stats(7);
?>
<h1>Dashboard</h1>
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
    <div class="card"><div class="card-body"><strong>Tổng bài</strong><p><?= $totalPosts; ?></p></div></div>
    <div class="card"><div class="card-body"><strong>Đang public</strong><p><?= $publishedPosts; ?></p></div></div>
    <div class="card"><div class="card-body"><strong>PV hôm nay</strong><p><?= $pvRow['pageviews']; ?></p></div></div>
    <div class="card"><div class="card-body"><strong>Unique hôm nay</strong><p><?= $pvRow['unique_visitors']; ?></p></div></div>
    <div class="card"><div class="card-body"><strong>Click Shopee hôm nay</strong><p><?= $clicksTodayVal; ?></p></div></div>
    <div class="card"><div class="card-body"><strong>Click Shopee tổng</strong><p><?= $clicksTotal; ?></p></div></div>
</div>
<canvas id="chart" height="120"></canvas>
<script>
const ctx = document.getElementById('chart');
const data = <?= json_encode($chartData); ?>;
const labels = data.map(d=>d.visit_date);
const pv = data.map(d=>parseInt(d.pageviews));
const uv = data.map(d=>parseInt(d.unique_visitors));
const clicks = data.map(d=>parseInt(d.valid_clicks));
new Chart(ctx, {
    type: 'line',
    data: {
        labels,
        datasets: [
            {label:'Pageviews', data: pv, borderColor:'#ff7043', fill:false},
            {label:'Unique', data: uv, borderColor:'#42a5f5', fill:false},
            {label:'Shopee clicks', data: clicks, borderColor:'#66bb6a', fill:false}
        ]
    },
});
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
