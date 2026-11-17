<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Models\User;

final class UserController
{
    public function index(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $users = User::paginate($search, max(1, (int) ($_GET['page'] ?? 1)), 15);

        View::render('admin/users/index', [
            'title'  => 'Customers & staff',
            'users'  => $users['items'],
            'total'  => $users['total'],
            'page'   => $users['page'],
            'pages'  => $users['pages'],
            'search' => $search,
        ], 'layouts/admin');
    }

    public function updateRole(array $params): void
    {
        $user = User::find((int) ($params['id'] ?? 0));

        if ($user === null) {
            abort(404);
        }

        $role = (string) ($_POST['role'] ?? '');

        if (! in_array($role, ['customer', 'admin'], true)) {
            flash('Choose a valid role.', 'error');
            redirect('/admin/users');
        }

        if ((int) $user['id'] === (int) Auth::id() && $role !== 'admin') {
            flash('You cannot remove your own admin rights.', 'error');
            redirect('/admin/users');
        }

        if ($user['role'] === 'admin' && $role === 'customer' && User::adminCount() <= 1) {
            flash('The store needs at least one administrator.', 'error');
            redirect('/admin/users');
        }

        User::setRole((int) $user['id'], $role);

        flash($user['full_name'] . ' is now ' . ($role === 'admin' ? 'an administrator' : 'a customer') . '.');

        redirect('/admin/users');
    }

    public function toggleActive(array $params): void
    {
        $user = User::find((int) ($params['id'] ?? 0));

        if ($user === null) {
            abort(404);
        }

        if ((int) $user['id'] === (int) Auth::id()) {
            flash('You cannot deactivate your own account.', 'error');
            redirect('/admin/users');
        }

        $active = (int) $user['is_active'] !== 1;
        User::setActive((int) $user['id'], $active);

        flash($user['full_name'] . ' has been ' . ($active ? 'activated' : 'deactivated') . '.');

        redirect('/admin/users');
    }
}
