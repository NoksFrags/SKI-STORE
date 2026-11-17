<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Category;
use App\Models\Product;

final class HomeController
{
    public function index(): void
    {
        View::render('home/index', [
            'title'        => 'Alpine gear for every mountain day',
            'categories'   => Category::all(),
            'featured'     => Product::featured(4),
            'newest'       => Product::newest(3),
            'productCount' => Product::stats()['active'],
        ]);
    }
}
