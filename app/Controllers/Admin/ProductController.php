<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Validator;
use App\Core\View;
use App\Models\Category;
use App\Models\Product;
use App\Services\UploadService;
use RuntimeException;

final class ProductController
{
    public function index(): void
    {
        $filters = [
            'search'           => trim((string) ($_GET['q'] ?? '')),
            'category'         => trim((string) ($_GET['category'] ?? '')),
            'status'           => (string) ($_GET['status'] ?? ''),
            'include_inactive' => true,
        ];

        $products = Product::paginate($filters, max(1, (int) ($_GET['page'] ?? 1)), 12);

        View::render('admin/products/index', [
            'title'      => 'Products',
            'products'   => $products['items'],
            'total'      => $products['total'],
            'page'       => $products['page'],
            'pages'      => $products['pages'],
            'categories' => Category::all(),
            'filters'    => $filters,
        ], 'layouts/admin');
    }

    public function create(): void
    {
        View::render('admin/products/form', [
            'title'      => 'New product',
            'product'    => null,
            'categories' => Category::all(),
        ], 'layouts/admin');
    }

    public function store(): void
    {
        $data = $this->payload();
        $errors = $this->validate($data, null, true);

        $data['slug'] = $this->uniqueSlug($data['slug'] !== '' ? $data['slug'] : slugify($data['name']), null);

        try {
            $data['image'] = UploadService::storeProductImage($_FILES['image'] ?? null);
        } catch (RuntimeException $exception) {
            $errors['image'] = $exception->getMessage();
            $data['image'] = null;
        }

        if ($errors !== []) {
            remember_old_input($_POST, $errors);
            redirect('/admin/products/create');
        }

        Product::create($data);

        flash('Product "' . $data['name'] . '" has been created.');

        redirect('/admin/products');
    }

    public function edit(array $params): void
    {
        $product = Product::find((int) ($params['id'] ?? 0));

        if ($product === null) {
            abort(404);
        }

        View::render('admin/products/form', [
            'title'      => 'Edit ' . $product['name'],
            'product'    => $product,
            'categories' => Category::all(),
        ], 'layouts/admin');
    }

    public function update(array $params): void
    {
        $product = Product::find((int) ($params['id'] ?? 0));

        if ($product === null) {
            abort(404);
        }

        $data = $this->payload();
        $errors = $this->validate($data, (int) $product['id'], false);

        $data['slug'] = $this->uniqueSlug($data['slug'] !== '' ? $data['slug'] : slugify($data['name']), (int) $product['id']);

        try {
            $data['image'] = UploadService::storeProductImage($_FILES['image'] ?? null, $product['image']);
        } catch (RuntimeException $exception) {
            $errors['image'] = $exception->getMessage();
            $data['image'] = $product['image'];
        }

        if ($errors !== []) {
            remember_old_input($_POST, $errors);
            redirect('/admin/products/' . $product['id'] . '/edit');
        }

        Product::update((int) $product['id'], $data);

        flash('Product "' . $data['name'] . '" has been updated.');

        redirect('/admin/products');
    }

    public function destroy(array $params): void
    {
        $product = Product::find((int) ($params['id'] ?? 0));

        if ($product === null) {
            abort(404);
        }

        UploadService::remove($product['image']);
        Product::delete((int) $product['id']);

        flash('Product "' . $product['name'] . '" has been deleted.', 'info');

        redirect('/admin/products');
    }

    private function payload(): array
    {
        return [
            'name'              => trim((string) ($_POST['name'] ?? '')),
            'slug'              => trim((string) ($_POST['slug'] ?? '')),
            'brand'             => trim((string) ($_POST['brand'] ?? '')),
            'sku'               => trim((string) ($_POST['sku'] ?? '')),
            'category_id'       => (int) ($_POST['category_id'] ?? 0) ?: null,
            'short_description' => trim((string) ($_POST['short_description'] ?? '')),
            'description'       => trim((string) ($_POST['description'] ?? '')),
            'price'             => trim((string) ($_POST['price'] ?? '0')),
            'compare_at_price'  => trim((string) ($_POST['compare_at_price'] ?? '')),
            'stock'             => trim((string) ($_POST['stock'] ?? '0')),
            'is_featured'       => isset($_POST['is_featured']),
            'is_active'         => isset($_POST['is_active']),
            'image'             => null,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validate(array $data, ?int $ignoreId, bool $isNew): array
    {
        $validator = (new Validator($_POST))
            ->required('name', 'Product name')
            ->maxLength('name', 180, 'Product name')
            ->maxLength('brand', 80, 'Brand')
            ->maxLength('sku', 40, 'SKU')
            ->maxLength('short_description', 255, 'Short description')
            ->decimal('price', 'Price')
            ->integer('stock', 'Stock')
            ->maxLength('slug', 200, 'Slug');

        $errors = $validator->errors();

        if ($data['category_id'] !== null && Category::find((int) $data['category_id']) === null) {
            $errors['category_id'] = 'Choose a valid category.';
        }

        if ((float) $data['price'] <= 0) {
            $errors['price'] = 'Price must be greater than zero.';
        }

        if ((int) $data['stock'] < 0) {
            $errors['stock'] = 'Stock cannot be negative.';
        }

        if ($data['compare_at_price'] !== '' && (float) $data['compare_at_price'] <= (float) $data['price']) {
            $errors['compare_at_price'] = 'The compare-at price should be higher than the price.';
        }

        if ($data['slug'] !== '' && Product::slugExists(slugify($data['slug']), $ignoreId)) {
            $errors['slug'] = 'That slug is already in use.';
        }

        return $errors;
    }

    private function uniqueSlug(string $base, ?int $ignoreId): string
    {
        $slug = slugify($base);
        $candidate = $slug;
        $counter = 2;

        while (Product::slugExists($candidate, $ignoreId)) {
            $candidate = $slug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }
}
