<?php
require __DIR__ . '/../app/config/config.php';
require __DIR__ . '/../app/lib/db.php';
require __DIR__ . '/../app/lib/auth.php';
start_secure_session($config);
$pdo = db($config);

$labels = [];
$pageviews = [];
$adClicks = [];
for ($i=6;$i>=0;$i--) {
    $day = date('Y-m-d', strtotime("-{$i} days"));
    $labels[] = $day;
    $pv = $pdo->prepare('SELECT COUNT(*) FROM click_events WHERE event_type="article_view" AND DATE(created_at)=?');
    $pv->execute([$day]);
    $pageviews[] = (int)$pv->fetchColumn();
    $ad = $pdo->prepare('SELECT COUNT(*) FROM click_events WHERE event_type="ad_forced_redirect" AND DATE(created_at)=?');
    $ad->execute([$day]);
    $adClicks[] = (int)$ad->fetchColumn();
}
header('Content-Type: application/json');
echo json_encode(['labels'=>$labels,'pageviews'=>$pageviews,'ad_clicks'=>$adClicks]);
