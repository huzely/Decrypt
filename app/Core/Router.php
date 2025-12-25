<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $uri, string $method)
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $path = rtrim($path, '/') ?: '/';
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = '#^' . preg_replace('#\{([^/]+)\}#', '(?P<$1>[^/]+)', $route) . '$#';
            if (preg_match($pattern, $path, $matches)) {
                return call_user_func($handler, $matches);
            }
        }
        http_response_code(404);
        echo 'Page not found';
        return null;
    }
}
