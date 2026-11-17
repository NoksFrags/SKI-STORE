<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Flash;

final class AdminMiddleware
{
    public function handle(): ?string
    {
        if (! Auth::check()) {
            Flash::add('warning', 'Please sign in to reach the admin area.');

            return '/login';
        }

        if (! Auth::isAdmin()) {
            abort(403, 'This area is reserved for store administrators.');
        }

        return null;
    }
}
