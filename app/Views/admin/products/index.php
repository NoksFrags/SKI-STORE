<?php

use App\Core\View;

$statusOptions = [
    ''             => 'All statuses',
    'active'       => 'Active',
    'inactive'     => 'Inactive',
    'low_stock'    => 'Low stock',
    'out_of_stock' => 'Out of stock',
];
?>
<div class="admin-page-head">
    <div>
        <h1>Products</h1>
        <p><?= (int) $total ?> product<?= (int) $total === 1 ? '' : 's' ?> in the catalogue.</p>
    </div>
    <div class="admin-page-head__actions">
        <a class="btn btn--primary btn--sm" href="<?= url('/admin/products/create') ?>"><?= icon('plus', 16) ?> New product</a>
    </div>
</div>

<form class="admin-filterbar" method="get" action="<?= url('/admin/products') ?>">
    <input class="input" type="search" name="q" value="<?= e($filters['search'] ?? '') ?>" placeholder="Search name, brand, SKU&hellip;" aria-label="Search products">
    <select class="select" name="category" aria-label="Filter by category">
        <option value="">All categories</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= e($category['slug']) ?>"<?= selected($filters['category'] ?? '', $category['slug']) ?>><?= e($category['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <select class="select" name="status" aria-label="Filter by status">
        <?php foreach ($statusOptions as $value => $label): ?>
            <option value="<?= e($value) ?>"<?= selected($filters['status'] ?? '', $value) ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn--outline btn--sm" type="submit"><?= icon('filter', 15) ?> Filter</button>
    <?php if (($filters['search'] ?? '') !== '' || ($filters['category'] ?? '') !== '' || ($filters['status'] ?? '') !== ''): ?>
        <a class="btn btn--ghost btn--sm" href="<?= url('/admin/products') ?>">Reset</a>
    <?php endif; ?>
</form>

<div class="panel">
    <div class="panel__body panel__body--flush">
        <?php if ($products === []): ?>
            <div class="empty" style="border:0;border-radius:0;padding:44px 20px">
                <span class="empty__icon"><?= icon('package', 26) ?></span>
                <div class="empty__title">No products found</div>
                <p class="empty__text">Adjust the filters or add the first product to the catalogue.</p>
                <a class="btn btn--primary btn--sm" href="<?= url('/admin/products/create') ?>">Add product</a>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <?php $stock = (int) $product['stock']; ?>
                            <tr>
                                <td>
                                    <div class="table__product">
                                        <span class="table__thumb">
                                            <img src="<?= e(product_image($product['image'])) ?>" alt="">
                                        </span>
                                        <span>
                                            <span class="table__name"><?= e($product['name']) ?></span>
                                            <span class="table__sub"><?= e($product['brand'] ?: 'No brand') ?> &middot; <?= e($product['sku'] ?: 'no SKU') ?></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="muted"><?= e($product['category_name'] ?? 'Uncategorised') ?></td>
                                <td class="num">
                                    <strong><?= e(money((float) $product['price'])) ?></strong>
                                    <?php if ($product['compare_at_price'] !== null): ?>
                                        <div class="table__sub" style="text-decoration:line-through"><?= e(money((float) $product['compare_at_price'])) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($stock < 1): ?>
                                        <span class="badge badge--danger">Sold out</span>
                                    <?php elseif ($stock <= (int) config('app.low_stock_threshold', 5)): ?>
                                        <span class="badge badge--warning"><?= $stock ?> left</span>
                                    <?php else: ?>
                                        <span class="num"><?= $stock ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ((int) $product['is_active'] === 1): ?>
                                        <span class="badge badge--success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge--muted">Inactive</span>
                                    <?php endif; ?>
                                    <?php if ((int) $product['is_featured'] === 1): ?>
                                        <span class="badge badge--info">Featured</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table__actions">
                                        <a class="btn btn--ghost btn--sm" href="<?= e(url('/product/' . $product['slug'])) ?>" target="_blank" rel="noopener" title="View in store"><?= icon('eye', 16) ?></a>
                                        <a class="btn btn--outline btn--sm" href="<?= url('/admin/products/' . $product['id'] . '/edit') ?>"><?= icon('edit', 15) ?> Edit</a>
                                        <form method="post" action="<?= url('/admin/products/' . $product['id'] . '/delete') ?>"
                                              data-confirm="Delete &quot;<?= e($product['name']) ?>&quot;? This cannot be undone.">
                                            <?= csrf_field() ?>
                                            <button class="btn btn--danger btn--sm" type="submit" title="Delete"><?= icon('trash', 15) ?></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($products !== []): ?>
    <?php View::partial('partials/pagination', ['page' => $page, 'pages' => $pages]); ?>
<?php endif; ?>
