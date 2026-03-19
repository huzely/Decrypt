<?php
require_once __DIR__ . '/../includes/header.php';
$filters = [
    'category' => $_GET['category'] ?? '',
    'tag' => $_GET['tag'] ?? '',
    'q' => $_GET['q'] ?? '',
];
$videos = fetch_videos($filters);
$categories = get_categories();
?>
<section class="filters panel">
    <div class="chip-row">
        <a class="chip <?php echo empty($filters['category']) ? 'active' : ''; ?>" href="index.php">All</a>
        <?php foreach ($categories as $category): ?>
            <a class="chip <?php echo $filters['category'] === $category['name'] ? 'active' : ''; ?>" href="?category=<?php echo urlencode($category['name']); ?>"><?php echo esc($category['name']); ?></a>
        <?php endforeach; ?>
    </div>
    <div class="chip-row">
        <?php foreach (get_tags() as $tag): ?>
            <a class="chip <?php echo $filters['tag'] === $tag['name'] ? 'active' : ''; ?>" href="?tag=<?php echo urlencode($tag['name']); ?>">#<?php echo esc($tag['name']); ?></a>
        <?php endforeach; ?>
    </div>
</section>
<section class="video-grid">
    <?php foreach ($videos as $video): ?>
        <a class="video-card" href="video.php?id=<?php echo (int) $video['id']; ?>">
            <div class="thumb-wrap"><img src="<?php echo esc($video['thumbnail']); ?>" alt="<?php echo esc($video['title']); ?>"></div>
            <div class="video-meta-card">
                <h3><?php echo esc($video['title']); ?></h3>
                <p><?php echo number_format((int) $video['views']); ?> views</p>
                <span><?php echo format_date($video['created_at']); ?></span>
            </div>
        </a>
    <?php endforeach; ?>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
