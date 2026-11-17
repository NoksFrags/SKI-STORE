<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\Order;

final class OrderController
{
    public function index(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? '');

        $orders = Order::paginateAdmin($search, $status, max(1, (int) ($_GET['page'] ?? 1)));

        View::render('admin/orders/index', [
            'title'    => 'Orders',
            'orders'   => $orders['items'],
            'total'    => $orders['total'],
            'page'     => $orders['page'],
            'pages'    => $orders['pages'],
            'search'   => $search,
            'status'   => $status,
            'statuses' => Order::STATUSES,
        ], 'layouts/admin');
    }

    public function show(array $params): void
    {
        $order = Order::find((int) ($params['id'] ?? 0));

        if ($order === null) {
            abort(404);
        }

        View::render('admin/orders/show', [
            'title'    => 'Order ' . $order['order_number'],
            'order'    => $order,
            'items'    => Order::items((int) $order['id']),
            'statuses' => Order::STATUSES,
        ], 'layouts/admin');
    }

    public function updateStatus(array $params): void
    {
        $order = Order::find((int) ($params['id'] ?? 0));

        if ($order === null) {
            abort(404);
        }

        $status = (string) ($_POST['status'] ?? '');

        if (! array_key_exists($status, Order::STATUSES)) {
            flash('Choose a valid order status.', 'error');
            redirect('/admin/orders/' . $order['id']);
        }

        Order::updateStatus((int) $order['id'], $status);

        flash('Order ' . $order['order_number'] . ' is now ' . Order::statusLabel($status) . '.');

        redirect('/admin/orders/' . $order['id']);
    }
}
