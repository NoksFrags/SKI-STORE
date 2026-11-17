<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

final class DashboardController
{
    public function index(): void
    {
        View::render('admin/dashboard', [
            'title'    => 'Overview',
            'orders'   => Order::stats(),
            'products' => Product::stats(),
            'customers' => User::countCustomers(),
            'newCustomers' => User::countNewThisMonth(),
            'revenue'  => Order::monthlyRevenue(6),
            'recent'   => Order::recent(6),
            'lowStock' => Product::lowStock(5),
            'topProducts' => Product::topSelling(5),
        ], 'layouts/admin');
    }
}
