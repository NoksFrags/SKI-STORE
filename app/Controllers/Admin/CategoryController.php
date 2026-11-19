<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Validator;
use App\Core\View;
use App\Models\Category;

final class CategoryController
{
    private const ICONS = ['ski', 'snowboard', 'boot', 'jacket', 'helmet', 'gear', 'snow'];

    public function index(): void
    {
        View::render('admin/categories/index', [
            'title'      => 'Categories',
            'categories' => Category::all(),
            'icons'      => self::ICONS,
        ], 'layouts/admin');
    }

    public function store(): void
    {
        $data = $this->payload();
        $errors = $this->validate($data, null);

        if ($errors !== []) {
            remember_old_input($_POST, $errors);
            redirect('/admin/categories');
        }

        Category::create($data);
        flash('Category "' . $data['name'] . '" has been created.');

        redirect('/admin/categories');
    }

    public function edit(array $params): void
    {
        $category = Category::find((int) ($params['id'] ?? 0));

        if ($category === null) {
            abort(404);
        }

        View::render('admin/categories/edit', [
            'title'    => 'Edit ' . $category['name'],
            'category' => $category,
            'icons'    => self::ICONS,
        ], 'layouts/admin');
    }

    public function update(array $params): void
    {
        $category = Category::find((int) ($params['id'] ?? 0));

        if ($category === null) {
            abort(404);
        }

        $data = $this->payload();
        $errors = $this->validate($data, (int) $category['id']);

        if ($errors !== []) {
            remember_old_input($_POST, $errors);
            redirect('/admin/categories/' . $category['id'] . '/edit');
        }

        Category::update((int) $category['id'], $data);
        flash('Category "' . $data['name'] . '" has been updated.');

        redirect('/admin/categories');
    }

    public function destroy(array $params): void
    {
        $category = Category::find((int) ($params['id'] ?? 0));

        if ($category === null) {
            abort(404);
        }

        if (Category::productCount((int) $category['id']) > 0) {
            flash('Move or delete the products in "' . $category['name'] . '" before removing the category.', 'error');
            redirect('/admin/categories');
        }

        Category::delete((int) $category['id']);
        flash('Category "' . $category['name'] . '" has been deleted.', 'info');

        redirect('/admin/categories');
    }

    private function payload(): array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));

        return [
            'name'        => $name,
            'slug'        => slugify($slug !== '' ? $slug : $name),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'icon'        => in_array($_POST['icon'] ?? '', self::ICONS, true) ? (string) $_POST['icon'] : 'snow',
            'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
        ];
    }

    private function validate(array $data, ?int $ignoreId): array
    {
        $validator = (new Validator($_POST))
            ->required('name', 'Category name')
            ->maxLength('name', 100, 'Category name');

        $errors = $validator->errors();

        if (Category::slugExists($data['slug'], $ignoreId)) {
            $errors['slug'] = 'Another category already uses that slug.';
        }

        return $errors;
    }
}
