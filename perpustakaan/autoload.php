<?php
// autoload.php — PSR-4 style manual autoloader

spl_autoload_register(function (string $class): void {
    $base = __DIR__ . '/';
    $map  = [
        'App\\Models\\'      => 'app/Models/',
        'App\\Controllers\\' => 'app/Controllers/',
        'App\\Config\\'      => 'config/',
    ];
    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
            $file     = $base . $dir . $relative . '.php';
            if (file_exists($file)) require_once $file;
            return;
        }
    }
});
