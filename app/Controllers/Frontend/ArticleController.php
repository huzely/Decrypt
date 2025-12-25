<?php
namespace App\Controllers\Frontend;

use App\Core\Config;
use App\Core\Security;
use App\Core\View;
use App\Models\Article;
use App\Models\Setting;
use App\Models\Stat;

class ArticleController
{
    public function show(array $params): void
    {
        $slug = $params['slug'] ?? '';
        $article = Article::findBySlug($slug);
        if (!$article) {
            http_response_code(404);
            echo 'Article not found';
            return;
        }
        $settings = Setting::getAll();
        $botList = $settings['bot_list'] ?? ['bot', 'crawler', 'spider'];
        if (!is_bot($botList)) {
            Stat::trackView((int)$article['id'], client_fingerprint(), $settings);
        }
        $related = Article::related((int)$article['category_id'], (int)$article['id']);
        View::render('frontend/article', [
            'article' => $article,
            'settings' => $settings,
            'related' => $related,
            'categories' => \App\Models\Category::all(),
        ]);
    }

    public function clickShopee(): void
    {
        verify_csrf();
        $articleId = (int)($_POST['article_id'] ?? 0);
        $settings = Setting::getAll();
        $botList = $settings['bot_list'] ?? ['bot', 'crawler'];
        $success = false;
        if ($articleId && !is_bot($botList)) {
            $success = Stat::trackClick($articleId, client_fingerprint(), $settings);
            if (!empty($settings['telegram_click_enabled'])) {
                $this->notifyTelegram('Click Shopee mới', 'Bài #' . $articleId . ' vừa nhận click Shopee.');
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['ok' => $success]);
    }

    private function notifyTelegram(string $title, string $message): void
    {
        $settings = Setting::getAll();
        $token = $settings['telegram_token'] ?? '';
        $chatId = $settings['telegram_chat_id'] ?? '';
        if (!$token || !$chatId) {
            return;
        }
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        $payload = http_build_query([
            'chat_id' => $chatId,
            'text' => $title . "\n" . $message,
        ]);
        @file_get_contents($url . '?' . $payload);
    }
}
