<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION['_token'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(self::token()) . '">';
    }

    public static function verifyOrAbort(): void
    {
        $token = $_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        if (! is_string($token) || $token === '' || ! hash_equals(self::token(), $token)) {
            abort(419, 'Your security token expired. Go back, reload the page and try again.');
        }
    }
}
