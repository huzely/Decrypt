<?php
require __DIR__ . '/../app/Core/init.php';

use App\Core\Router;
use App\Controllers\Frontend\HomeController;
use App\Controllers\Frontend\ArticleController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ArticleController as AdminArticleController;
use App\Controllers\Admin\SettingsController;
use App\Controllers\Admin\ReportController;

$router = new Router();

// Frontend
$router->get('/', fn() => (new HomeController())->index());
$router->get('/article/{slug}', fn($params) => (new ArticleController())->show($params));
$router->post('/track-click', fn() => (new ArticleController())->clickShopee());
// Simple slug route
$router->get('/{slug}', fn($params) => (new ArticleController())->show($params));

// Admin
$router->get('/admin', fn() => (new DashboardController())->index());
$router->get('/admin/login', fn() => (new AuthController())->showLogin());
$router->post('/admin/login', fn() => (new AuthController())->login());
$router->get('/admin/logout', fn() => (new AuthController())->logout());
$router->post('/admin/reset-stats', fn() => (new DashboardController())->resetStats());
$router->get('/admin/articles', fn() => (new AdminArticleController())->index());
$router->get('/admin/articles/create', fn() => (new AdminArticleController())->create());
$router->post('/admin/articles/store', fn() => (new AdminArticleController())->store());
$router->get('/admin/articles/edit/{id}', fn($params) => (new AdminArticleController())->edit($params));
$router->post('/admin/articles/update/{id}', fn($params) => (new AdminArticleController())->update($params));
$router->post('/admin/articles/delete/{id}', fn($params) => (new AdminArticleController())->delete($params));
$router->post('/admin/articles/copy/{id}', fn($params) => (new AdminArticleController())->copy($params));
$router->get('/admin/settings', fn() => (new SettingsController())->index());
$router->post('/admin/settings', fn() => (new SettingsController())->save());
$router->get('/admin/export', fn() => (new ReportController())->exportCsv());

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
