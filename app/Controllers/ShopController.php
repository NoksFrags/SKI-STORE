<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Category;
use App\Models\Product;

final class ShopController
{
    public function index(): void
    {
        $filters = [
            'search'   => trim((string) ($_GET['q'] ?? '')),
            'category' => trim((string) ($_GET['category'] ?? '')),
            'sort'     => (string) ($_GET['sort'] ?? 'newest'),
        ];

        $page = max(1, (int) ($_GET['page'] ?? 1));

        $products = Product::paginate($filters, $page, (int) config('app.per_page'));
        $activeCategory = $filters['category'] !== '' ? Category::findBySlug($filters['category']) : null;

        View::render('shop/index', [
            'title'          => $activeCategory !== null ? (string) $activeCategory['name'] : 'Shop all gear',
            'products'       => $products['items'],
            'total'          => $products['total'],
            'page'           => $products['page'],
            'pages'          => $products['pages'],
            'categories'     => Category::all(),
            'filters'        => $filters,
            'activeCategory' => $activeCategory,
        ]);
    }

    public function show(array $params): void
    {
        $product = Product::findBySlug((string) ($params['slug'] ?? ''));

        if ($product === null || (int) $product['is_active'] !== 1) {
            abort(404);
        }

        View::render('shop/show', [
            'title'    => (string) $product['name'],
            'product'  => $product,
            'related'  => Product::related($product, 3),
        ]);
    }
}
