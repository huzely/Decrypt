<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Movie.php';

class HomeController
{
    public function index(): void
    {
        $movieModel = new Movie();
        $sections = [
            'Phim mới cập nhật' => 'series',
            'Phim chiếu rạp' => 'theater',
            'Phim bộ' => 'series',
            'Phim lẻ' => 'single',
            'Phim Netflix đề cử' => 'netflix',
            'Phim hành động' => 'Hành Động',
            'Phim kinh dị' => 'Kinh Dị',
            'Phim hoạt hình' => 'Hoạt Hình',
        ];

        $featured = $movieModel->featured();
        $moviesBySection = [];
        foreach ($sections as $label => $key) {
            $moviesBySection[$label] = $movieModel->byCategory($key);
        }

        include __DIR__ . '/../views/home/index.php';
    }
}
