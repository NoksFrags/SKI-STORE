<?php

use App\Core\View;

$errors = errors();
?>
<div class="admin-page-head">
    <div>
        <h1>Categories</h1>
        <p><?= count($categories) ?> departments organise the catalogue and the storefront navigation.</p>
    </div>
</div>

<div class="admin-grid admin-grid--2">
    <div class="panel">
        <div class="panel__head"><h2>All categories</h2><span class="small muted">Sorted by display order</span></div>
        <div class="panel__body panel__body--flush">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th>Category</th><th>Slug</th><th>Products</th><th>Order</th><th style="text-align:right">Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <div class="table__product">
                                        <span class="category-card__icon" style="width:36px;height:36px;border-radius:9px">
                                            <?= View::capture('partials/category-icon', ['icon' => $category['icon'], 'size' => 18]) ?>
                                        </span>
                                        <span>
                                            <span class="table__name"><?= e($category['name']) ?></span>
                                            <span class="table__sub"><?= e(excerpt($category['description'] ?? '', 44)) ?></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="muted"><?= e($category['slug']) ?></td>
                                <td class="num"><?= (int) $category['product_count'] ?></td>
                                <td class="num"><?= (int) $category['sort_order'] ?></td>
                                <td>
                                    <div class="table__actions">
                                        <a class="btn btn--outline btn--sm" href="<?= url('/admin/categories/' . $category['id'] . '/edit') ?>"><?= icon('edit', 15) ?> Edit</a>
                                        <form method="post" action="<?= url('/admin/categories/' . $category['id'] . '/delete') ?>"
                                              data-confirm="Delete category &quot;<?= e($category['name']) ?>&quot;?">
                                            <?= csrf_field() ?>
                                            <button class="btn btn--danger btn--sm" type="submit"
                                                <?= (int) $category['product_count'] > 0 ? 'disabled title="Move or delete its products first"' : '' ?>>
                                                <?= icon('trash', 15) ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel__head"><h2>New category</h2></div>
        <div class="panel__body">
            <form method="post" action="<?= url('/admin/categories') ?>">
                <?= csrf_field() ?>

                <div class="form-grid">
                    <div class="field field--full">
                        <label class="field__label" for="name">Name <span class="req">*</span></label>
                        <input class="input <?= isset($errors['name']) ? 'input--error' : '' ?>" type="text" id="name" name="name" value="<?= e(old('name')) ?>" required>
                        <?php if (isset($errors['name'])): ?><span class="field__error"><?= e($errors['name']) ?></span><?php endif; ?>
                    </div>

                    <div class="field field--full">
                        <label class="field__label" for="slug">Slug</label>
                        <input class="input <?= isset($errors['slug']) ? 'input--error' : '' ?>" type="text" id="slug" name="slug" value="<?= e(old('slug')) ?>" placeholder="auto-generated from the name">
                        <?php if (isset($errors['slug'])): ?><span class="field__error"><?= e($errors['slug']) ?></span><?php endif; ?>
                    </div>

                    <div class="field field--full">
                        <label class="field__label" for="description">Description</label>
                        <textarea class="textarea" id="description" name="description" rows="3"><?= e(old('description')) ?></textarea>
                    </div>

                    <div class="field">
                        <label class="field__label" for="icon">Icon</label>
                        <select class="select" id="icon" name="icon">
                            <?php foreach ($icons as $icon): ?>
                                <option value="<?= e($icon) ?>"<?= selected(old('icon', 'snow'), $icon) ?>><?= e(ucfirst($icon)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label class="field__label" for="sort_order">Sort order</label>
                        <input class="input" type="number" step="1" id="sort_order" name="sort_order" value="<?= e(old('sort_order', '0')) ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn btn--primary" type="submit"><?= icon('plus', 16) ?> Create category</button>
                </div>
            </form>
        </div>
    </div>
</div>
