<?php

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Emulate Apache "mod_rewrite" functionality for PHP built-in server.
// Only bypass Laravel for actual static files (not directories like /admin)
if ($uri !== '/' && is_file($publicPath . $uri)) {
    return false;
}

require_once $publicPath . '/index.php';
