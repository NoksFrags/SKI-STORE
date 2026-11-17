<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Validator;
use App\Core\View;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Models\Wishlist;

final class AccountController
{
    public function dashboard(): void
    {
        $userId = (int) Auth::id();
        $orders = Order::paginateForUser($userId, 1, 4);

        View::render('account/dashboard', [
            'title'         => 'My dashboard',
            'user'          => User::find($userId),
            'orders'        => $orders['items'],
            'orderCount'    => $orders['total'],
            'wishlistCount' => Wishlist::count($userId),
            'totalSpent'    => Order::totalSpentForUser($userId),
            'totals'        => Cart::totals(),
            'wishlistItems' => array_slice(Wishlist::items($userId), 0, 3),
        ]);
    }

    public function orders(): void
    {
        $orders = Order::paginateForUser((int) Auth::id(), max(1, (int) ($_GET['page'] ?? 1)));

        View::render('account/orders', [
            'title'  => 'My orders',
            'orders' => $orders['items'],
            'page'   => $orders['page'],
            'pages'  => $orders['pages'],
            'total'  => $orders['total'],
        ]);
    }

    public function order(array $params): void
    {
        $order = Order::findForUser((int) ($params['id'] ?? 0), (int) Auth::id());

        if ($order === null) {
            abort(404);
        }

        View::render('account/order', [
            'title' => 'Order ' . $order['order_number'],
            'order' => $order,
            'items' => Order::items((int) $order['id']),
        ]);
    }

    public function cancelOrder(array $params): void
    {
        $order = Order::findForUser((int) ($params['id'] ?? 0), (int) Auth::id());

        if ($order === null) {
            abort(404);
        }

        if ($order['status'] !== 'pending') {
            flash('Only orders that are still pending can be cancelled online. Please contact us for help.', 'error');
            redirect('/account/orders/' . $order['id']);
        }

        Order::updateStatus((int) $order['id'], 'cancelled');
        flash('Order ' . $order['order_number'] . ' has been cancelled.');

        redirect('/account/orders/' . $order['id']);
    }

    public function profile(): void
    {
        View::render('account/profile', [
            'title' => 'Profile details',
            'user'  => User::find((int) Auth::id()),
        ]);
    }

    public function updateProfile(): void
    {
        $userId = (int) Auth::id();

        $validator = (new Validator($_POST))
            ->required('full_name', 'Full name')
            ->maxLength('full_name', 120, 'Full name')
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->maxLength('email', 180, 'Email')
            ->maxLength('phone', 40, 'Phone')
            ->maxLength('address', 180, 'Address')
            ->maxLength('city', 100, 'City')
            ->maxLength('postal_code', 20, 'Postal code');

        if ($validator->passes() && User::emailExists((string) ($_POST['email'] ?? ''), $userId)) {
            $validator->add('email', 'This email is already used by another account.');
        }

        if ($validator->fails()) {
            remember_old_input($_POST, $validator->errors());
            redirect('/account/profile');
        }

        User::updateProfile($userId, [
            'full_name'   => (string) $_POST['full_name'],
            'email'       => (string) $_POST['email'],
            'phone'       => trim((string) ($_POST['phone'] ?? '')),
            'address'     => trim((string) ($_POST['address'] ?? '')),
            'city'        => trim((string) ($_POST['city'] ?? '')),
            'postal_code' => trim((string) ($_POST['postal_code'] ?? '')),
            'country'     => trim((string) ($_POST['country'] ?? '')),
        ]);

        flash('Your profile has been updated.');

        redirect('/account/profile');
    }

    public function password(): void
    {
        View::render('account/password', ['title' => 'Change password']);
    }

    public function updatePassword(): void
    {
        $user = User::find((int) Auth::id());

        if ($user === null) {
            redirect('/login');
        }

        $validator = (new Validator($_POST))
            ->required('current_password', 'Current password')
            ->required('password', 'New password')
            ->minLength('password', 8, 'New password')
            ->matches('password_confirmation', 'password', 'Password confirmation', 'the new password');

        if ($validator->passes() && ! password_verify((string) ($_POST['current_password'] ?? ''), (string) $user['password_hash'])) {
            $validator->add('current_password', 'Your current password is not correct.');
        }

        if ($validator->fails()) {
            remember_old_input($_POST, $validator->errors());
            redirect('/account/password');
        }

        User::updatePassword((int) $user['id'], password_hash((string) $_POST['password'], PASSWORD_DEFAULT));

        flash('Your password has been changed.');

        redirect('/account');
    }
}
