<?php

declare(strict_types=1);

/**
 * Router for PHP's built-in development server:
 *   php -S 127.0.0.1:8080 -t public public/router.php
 *
 * Existing files (CSS, JS, images, uploads) are served by the server itself;
 * everything else is handed to the front controller.
 */

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$candidate = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, urldecode($path));

if ($path !== '/' && is_file($candidate) && strpos(realpath($candidate) ?: '', realpath(__DIR__)) === 0) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';

require __DIR__ . '/index.php';
