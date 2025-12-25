<?php
namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;

class ArticleController extends BaseAdminController
{
    public function index(): void
    {
        $this->requireAuth();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $data = Article::paginate($page, 15, null, null, true);
        View::render('admin/articles/index', ['articles' => $data['items'], 'pagination' => $data]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $categories = Category::all();
        View::render('admin/articles/form', ['article' => null, 'categories' => $categories]);
    }

    public function store(): void
    {
        $this->requireAuth();
        verify_csrf();
        $data = $this->articleDataFromRequest(false);
        $id = Article::create($data);
        if ($data['include_id_tail']) {
            $data['slug'] = rtrim($data['slug'], '-') . '-' . $id;
            Article::update($id, $data);
        }
        $this->maybeNotifyTelegram($data['title']);
        redirect('admin/articles');
    }

    public function edit(array $params): void
    {
        $this->requireAuth();
        $id = (int)$params['id'];
        $article = Article::findById($id);
        $categories = Category::all();
        View::render('admin/articles/form', ['article' => $article, 'categories' => $categories]);
    }

    public function update(array $params): void
    {
        $this->requireAuth();
        verify_csrf();
        $id = (int)$params['id'];
        $data = $this->articleDataFromRequest(true, $id);
        Article::update($id, $data);
        redirect('admin/articles');
    }

    public function delete(array $params): void
    {
        $this->requireAuth();
        verify_csrf();
        Article::delete((int)$params['id']);
        redirect('admin/articles');
    }

    public function copy(array $params): void
    {
        $this->requireAuth();
        verify_csrf();
        Article::copy((int)$params['id']);
        redirect('admin/articles');
    }

    private function articleDataFromRequest(bool $hasId = false, int $id = 0): array
    {
        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?: slugify($title);
        $includeIdTail = !empty($_POST['include_id_tail']);
        $slugWithTail = $slug;
        if ($includeIdTail && $hasId) {
            $slugWithTail .= '-' . $id;
        }
        return [
            'title' => $title,
            'slug' => $slugWithTail,
            'meta_title' => $_POST['meta_title'] ?? $title,
            'meta_description' => $_POST['meta_description'] ?? '',
            'keywords' => $_POST['keywords'] ?? '',
            'og_image' => $_POST['og_image'] ?? '',
            'content' => $_POST['content'] ?? '',
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'tags' => $_POST['tags'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'include_id_tail' => $includeIdTail,
            'published_at' => $_POST['published_at'] ?: date('Y-m-d H:i:s'),
        ];
    }

    private function maybeNotifyTelegram(string $title): void
    {
        $settings = Setting::getAll();
        if (empty($settings['telegram_enabled'])) {
            return;
        }
        $token = $settings['telegram_token'] ?? '';
        $chatId = $settings['telegram_chat_id'] ?? '';
        if (!$token || !$chatId) {
            return;
        }
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        $payload = http_build_query([
            'chat_id' => $chatId,
            'text' => 'Bài mới: ' . $title,
        ]);
        @file_get_contents($url . '?' . $payload);
    }
}
