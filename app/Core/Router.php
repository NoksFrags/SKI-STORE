<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{

    private array $routes = [];

    private array $groupMiddleware = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function group(array $middleware, callable $callback): void
    {
        $previous = $this->groupMiddleware;
        $this->groupMiddleware = array_merge($previous, $middleware);

        $callback($this);

        $this->groupMiddleware = $previous;
    }

    private function add(string $method, string $path, array $handler, array $middleware): void
    {
        $this->routes[] = [
            'method'     => $method,
            'pattern'    => $this->compile($path),
            'handler'    => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    private function compile(string $path): string
    {
        $pattern = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#',
            static fn (array $match): string => '(?P<' . $match[1] . '>[^/]+)',
            $path
        );

        return '#^' . $pattern . '$#';
    }

    public function dispatch(string $method, string $path): void
    {
        $method = strtoupper($method);
        $method = $method === 'HEAD' ? 'GET' : $method;
        $pathMatched = false;

        foreach ($this->routes as $route) {
            if (! preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            $pathMatched = true;

            if ($route['method'] !== $method) {
                continue;
            }

            if ($method === 'POST') {
                Csrf::verifyOrAbort();
            }

            foreach ($route['middleware'] as $middleware) {

                $instance = new $middleware();
                $result = $instance->handle();

                if (is_string($result)) {
                    redirect($result);
                }
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            [$controller, $action] = $route['handler'];

            (new $controller())->{$action}($params);

            return;
        }

        abort($pathMatched ? 405 : 404);
    }
}
