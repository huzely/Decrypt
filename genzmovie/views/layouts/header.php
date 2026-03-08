<?php
require_once __DIR__ . '/../../models/Ad.php';
$adModel = new Ad();
$headerAds = $adModel->activeByPosition('header');
$sidebarAds = $adModel->activeByPosition('sidebar');
$popupAds = $adModel->activeByPosition('popup');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($metaTitle ?? APP_NAME) ?></title>
    <meta name="description" content="<?= e($metaDescription ?? 'Xem phim chất lượng cao tại GENZMOVIE') ?>">
    <meta name="keywords" content="<?= e($metaKeywords ?? 'phim, xem phim, vietsub') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/genzmovie/assets/css/style.css">
</head>
<body class="bg-dark text-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-secondary sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/genzmovie/"><?= APP_NAME ?></a>
        <form class="d-flex position-relative" action="/genzmovie/" method="GET">
            <input type="hidden" name="route" value="search">
            <input id="movie-search" class="form-control me-2" type="search" placeholder="Tìm tên phim, diễn viên..." name="q" autocomplete="off">
            <div id="autocomplete-box" class="autocomplete-box"></div>
        </form>
        <div>
            <?php if (!empty($_SESSION['user'])): ?>
                <a class="btn btn-outline-light btn-sm" href="/genzmovie/?route=dashboard">Tài khoản</a>
                <a class="btn btn-danger btn-sm" href="/genzmovie/?route=logout">Đăng xuất</a>
            <?php else: ?>
                <a class="btn btn-outline-light btn-sm" href="/genzmovie/?route=login">Đăng nhập</a>
                <a class="btn btn-danger btn-sm" href="/genzmovie/?route=register">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php foreach ($headerAds as $ad): ?>
    <div class="container py-2 ad-slot"><?= $ad['ad_code'] ?></div>
<?php endforeach; ?>

<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-lg-9">
