<?php
require __DIR__ . '/includes/header.php';

$stats = [
    'total_posts' => (int)$pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn(),
    'published' => (int)$pdo->query('SELECT COUNT(*) FROM articles WHERE status="published"')->fetchColumn(),
    'views_today' => (int)$pdo->query('SELECT COUNT(*) FROM click_events WHERE event_type="article_view" AND DATE(created_at)=CURDATE()')->fetchColumn(),
    'ad_today' => (int)$pdo->query('SELECT COUNT(*) FROM click_events WHERE event_type="ad_forced_redirect" AND DATE(created_at)=CURDATE()')->fetchColumn(),
];
?>
<h1>Dashboard</h1>
<div class="stats-grid">
    <div class="stat-card"><strong>Tổng bài</strong><div><?= $stats['total_posts']; ?></div></div>
    <div class="stat-card"><strong>Published</strong><div><?= $stats['published']; ?></div></div>
    <div class="stat-card"><strong>View hôm nay</strong><div><?= $stats['views_today']; ?></div></div>
    <div class="stat-card"><strong>Click Shopee hôm nay</strong><div><?= $stats['ad_today']; ?></div></div>
</div>
<canvas id="chart" height="140"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
fetch('/api/stats.php').then(r=>r.json()).then(data=>{
    new Chart(document.getElementById('chart'),{
        type:'line',
        data:{
            labels:data.labels,
            datasets:[
                {label:'Pageviews', borderColor:'#0d6efd', data:data.pageviews, fill:false},
                {label:'Ad clicks', borderColor:'#f59e0b', data:data.ad_clicks, fill:false},
            ]
        }
    });
});
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
