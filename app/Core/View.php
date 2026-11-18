<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        extract($data);
        $viewFile = dirname(__DIR__) . '/Views/' . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $view) . '.php';

        if (!is_file($viewFile)) {
            throw new \RuntimeException("Vue introuvable : $view");
        }

        if ($layout !== null) {
            $layoutFile = dirname(__DIR__) . '/Views/layouts/' . $layout . '.php';
            require $layoutFile;
            return;
        }

        require $viewFile;
    }

    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
