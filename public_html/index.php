<?php
$config = require __DIR__ . '/app/config/config.php';
require __DIR__ . '/app/helpers/util.php';
require __DIR__ . '/app/models/Article.php';
require __DIR__ . '/app/models/SiteSettings.php';

$settings = SiteSettings::get();

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (preg_match('#^/a/(\d+)-#', $requestUri, $matches)) {
    $articleId = (int)$matches[1];
    $cacheKey = 'article_' . $articleId;
    $article = cache_get($cacheKey, 180);
    if (!$article) {
        $article = Article::find($articleId);
        if ($article) {
            cache_set($cacheKey, $article);
        }
    }
    if (!$article || !$article['is_public']) {
        http_response_code(404);
        echo 'Bài viết không tồn tại.';
        exit;
    }
    include __DIR__ . '/app/views/article.php';
    exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total = Article::countPublic();
list($offset, $limit, $page, $pages) = paginate($total, 9, $page);
$articles = Article::latest($limit, $offset);
include __DIR__ . '/app/views/home.php';
