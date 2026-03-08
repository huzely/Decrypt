<?php

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/MovieController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/UserController.php';

$route = $_GET['route'] ?? 'home';

switch ($route) {
    case 'home':
        (new HomeController())->index();
        break;
    case 'movie':
        (new MovieController())->show($_GET['slug'] ?? '');
        break;
    case 'search':
        (new MovieController())->search();
        break;
    case 'filter':
        (new MovieController())->filter();
        break;
    case 'autocomplete':
        (new MovieController())->autocomplete();
        break;
    case 'login':
        (new AuthController())->login();
        break;
    case 'register':
        (new AuthController())->register();
        break;
    case 'logout':
        (new AuthController())->logout();
        break;
    case 'dashboard':
        (new UserController())->dashboard();
        break;
    case 'favorite':
        (new UserController())->toggleFavorite();
        break;
    default:
        http_response_code(404);
        echo '404 Not Found';
}
