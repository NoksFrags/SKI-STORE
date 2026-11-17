<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Flash;

final class AuthMiddleware
{
    public function handle(): ?string
    {
        if (Auth::check()) {
            return null;
        }

        Flash::add('warning', 'Please sign in to continue.');

        return '/login';
    }
}
