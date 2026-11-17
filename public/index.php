<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

$router = new App\Core\Router();

require BASE_PATH . '/routes/web.php';

$router->dispatch(
    (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'),
    request_path()
);
