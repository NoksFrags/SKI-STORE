<?php

declare(strict_types=1);

return [
    'name'                => (string) env('APP_NAME', 'Ski Store'),
    'env'                 => (string) env('APP_ENV', 'local'),
    'debug'               => filter_var((string) env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN),
    'url'                 => rtrim((string) env('APP_URL', ''), '/'),

    'currency'            => (string) env('CURRENCY', 'KM'),
    'currency_decimals'   => (int) env('CURRENCY_DECIMALS', '2'),
    'shipping_flat'       => (float) env('SHIPPING_FLAT', '9.90'),
    'free_shipping_from'  => (float) env('FREE_SHIPPING_FROM', '200.00'),

    'per_page'            => 9,
    'low_stock_threshold' => 5,

    'uploads'             => [
        'directory'   => PUBLIC_PATH . '/uploads/products',
        'url'         => '/uploads/products',
        'max_bytes'   => 3 * 1024 * 1024,
        'mime_types'  => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'],
    ],
];
