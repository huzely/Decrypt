<?php
namespace App\Controllers\Frontend;

use App\Core\View;
use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Stat;

class HomeController
{
    public function index(): void
    {
        $settings = Setting::getAll();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
        $search = $_GET['q'] ?? null;
        $data = Article::paginate($page, 8, $categoryId, $search);
        $categories = Category::all();
        View::render('frontend/home', [
            'articles' => $data['items'],
            'pagination' => $data,
            'categories' => $categories,
            'settings' => $settings,
            'search' => $search,
            'categoryId' => $categoryId,
        ]);
    }
}
