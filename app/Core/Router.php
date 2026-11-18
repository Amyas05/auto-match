<?php

namespace App\Core;

class Router
{
    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        $action = $segments[1] ?? 'index';
        $className = match ($segments[0] ?? '') {
            'catalogue' => 'App\\Controllers\\CatalogueController',
            'auth' => 'App\\Controllers\\AuthController',
            'annonce' => 'App\\Controllers\\AdController',
            default => 'App\\Controllers\\CatalogueController',
        };

        $methodName = $method === 'POST' ? 'store' : 'index';
        if (method_exists($className, $action)) {
            $methodName = $action;
        }

        $controller = new $className();
        if (!method_exists($controller, $methodName)) {
            http_response_code(404);
            echo 'Page introuvable';
            return;
        }
        $controller->{$methodName}();
    }
}
