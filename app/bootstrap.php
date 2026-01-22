<?php
declare(strict_types=1);

// Load core first (no autoloader magic for these)
require_once __DIR__ . '/Util.php';
require_once __DIR__ . '/Layout.php';

// Autoload the rest from app/
spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/' . $class . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});