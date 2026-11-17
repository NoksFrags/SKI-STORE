<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = [], string $layout = 'layouts/app'): void
    {
        $content = self::capture($template, $data);

        echo self::capture($layout, array_merge($data, ['content' => $content]));

        forget_transient_input();
    }

    public static function partial(string $template, array $data = []): void
    {
        echo self::capture($template, $data);
    }

    public static function capture(string $template, array $data = []): string
    {
        $file = APP_PATH . '/Views/' . $template . '.php';

        if (! is_file($file)) {
            throw new \RuntimeException('View not found: ' . $template);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $file;

        return (string) ob_get_clean();
    }
}
