<?php
declare(strict_types=1);

spl_autoload_register(function (string $class) {
    $prefix  = 'App\\';                     // namespace
    $baseDir = __DIR__ . '/src/';           // class docs in src

    // if namespace not start with prefix > get out
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }


    $relative = substr($class, $len);       
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    } else {
        throw new RuntimeException("Autoload: doc  is not found: {$class} ({$file})");
    }
});