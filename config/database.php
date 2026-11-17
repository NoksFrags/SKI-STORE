<?php

declare(strict_types=1);

return [
    'host'     => (string) env('DB_HOST', '127.0.0.1'),
    'port'     => (string) env('DB_PORT', '3306'),
    'database' => (string) env('DB_NAME', 'skistore'),
    'username' => (string) env('DB_USER', 'root'),
    'password' => (string) env('DB_PASS', ''),
    'charset'  => 'utf8mb4',
];
