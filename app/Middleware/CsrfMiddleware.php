<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;

final class CsrfMiddleware
{
    public function handle(): ?string
    {
        Csrf::verifyOrAbort();

        return null;
    }
}
