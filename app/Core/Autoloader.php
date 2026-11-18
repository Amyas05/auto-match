<?php

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register(function (string $class): void {
            $prefixes = [
                'App\\Core\\' => __DIR__ . '/',
                'App\\Controllers\\' => dirname(__DIR__) . '/Controllers/',
                'App\\Repositories\\' => dirname(__DIR__) . '/Repositories/',
                'App\\Services\\' => dirname(__DIR__) . '/Services/',
            ];

            foreach ($prefixes as $prefix => $directory) {
                if (str_starts_with($class, $prefix)) {
                    $relative = substr($class, strlen($prefix));
                    $file = $directory . str_replace('\\', '/', $relative) . '.php';
                    if (is_file($file)) {
                        require $file;
                    }
                    return;
                }
            }
        });
    }
}
