<?php
require_once __DIR__ . '/config.php';

$search = trim($_GET['q'] ?? '');

$announcementStmt = $pdo->query('SELECT title, content FROM announcements ORDER BY created_at DESC LIMIT 1');
$announcement = $announcementStmt->fetch();

if ($search !== '') {
    $stmt = $pdo->prepare('SELECT * FROM videos WHERE title LIKE :q ORDER BY created_at DESC');
    $stmt->execute(['q' => '%' . $search . '%']);
} else {
    $stmt = $pdo->query('SELECT * FROM videos ORDER BY created_at DESC LIMIT 24');
}
$videos = $stmt->fetchAll();

$adsStmt = $pdo->prepare("SELECT * FROM ads WHERE status = 1 AND type IN ('popup', 'banner', 'google') ORDER BY id DESC");
$adsStmt->execute();
$ads = $adsStmt->fetchAll();

$popupImage = '';
$popupLinks = [];
$bannerAds = [];
$googleCodes = [];

foreach ($ads as $ad) {
    if ($ad['type'] === 'popup' && $popupImage === '') {
        $popupImage = $ad['image'];
        $popupLinks = array_filter(array_map('trim', explode(',', (string) $ad['link'])));
    }
    if ($ad['type'] === 'banner') {
        $bannerAds[] = $ad;
    }
    if ($ad['type'] === 'google') {
        $googleCodes[] = $ad;
    }
}

$logo = setting($pdo, 'site_logo', '');
$banner = setting($pdo, 'site_banner', '');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AVSTube - Nền tảng Chia sẻ Video</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="header">
    <?php if ($logo !== ''): ?>
        <img src="<?= e($logo) ?>" alt="Logo" style="max-height:80px" class="mb-2">
    <?php endif; ?>
    <h1>AVSTube</h1>
    <p class="lead">Nền tảng chia sẻ video chất lượng cao</p>
    <p>Phiên bản mới nhất: V1.0 (2026)</p>
    <form class="mt-3 mx-auto" style="max-width: 520px" method="get" action="search.php">
        <div class="input-group">
            <input class="form-control bg-dark text-light border-secondary" name="q" placeholder="Tìm kiếm video..." value="<?= e($search) ?>">
            <button class="btn btn-red" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>
</header>

<?php if ($announcement): ?>
<div class="announcement-bar text-center">
    <strong><?= e($announcement['title']) ?>:</strong> <?= e($announcement['content']) ?>
</div>
<?php endif; ?>

<?php if ($banner !== ''): ?>
<section class="section pb-0">
    <div class="container"><img src="<?= e($banner) ?>" class="img-fluid rounded" alt="Banner"></div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container">
        <h2 class="text-center mb-5">Video Nổi Bật</h2>
        <?php if ($bannerAds): ?>
            <div class="row mb-4">
                <?php foreach ($bannerAds as $ad): ?>
                    <div class="col-md-6 mb-3">
                        <a href="<?= e($ad['link']) ?>" target="_blank" rel="noopener">
                            <img src="<?= e($ad['image']) ?>" class="img-fluid rounded" alt="Banner Ads">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php foreach ($googleCodes as $code): ?>
            <div class="panel-dark p-3 mb-4"><?= $code['image'] ?></div>
        <?php endforeach; ?>

        <div class="video-grid">
            <?php foreach ($videos as $video): ?>
                <a href="video.php?id=<?= (int) $video['id'] ?>" class="video-card">
                    <div class="thumbnail">
                        <img src="<?= e($video['thumbnail']) ?>" alt="<?= e($video['title']) ?>">
                        <span class="duration"><?= e($video['duration'] ?? '00:00') ?></span>
                    </div>
                    <div class="card-info">
                        <p class="title"><?= e($video['title']) ?></p>
                        <p class="views"><?= number_format((int) $video['views']) ?> lượt xem • <?= date('d/m/Y', strtotime($video['created_at'])) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="text-center mb-5">Đánh giá từ người dùng</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="p-4 bg-dark rounded">
                    <p><strong>Mike Felson / Webmaster</strong></p>
                    <p>"Nền tảng tốt nhất hiện nay. Đầy đủ tính năng, hỗ trợ HD, mobile mượt mà và đội ngũ hỗ trợ rất nhanh."</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer>
    <p>Địa chỉ: Hà Nội, Việt Nam</p>
    <p>Email: support@avstube.vn</p>
    <p>Copyright © 2026 AVSTube. All Rights Reserved.</p>
</footer>

<div id="popupOverlay" class="popup-overlay" data-image="<?= e($popupImage) ?>" data-links="<?= e(implode(',', $popupLinks)) ?>">
    <div class="popup-content">
        <button class="popup-close" aria-label="Close">&times;</button>
        <img src="" alt="Popup Ads">
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
