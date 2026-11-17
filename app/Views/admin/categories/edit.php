<?php

use App\Core\View;

$errors = errors();
?>
<div class="admin-page-head">
    <div>
        <h1>Edit category</h1>
        <p><?= e($category['name']) ?> &middot; <?= e($category['slug']) ?></p>
    </div>
    <div class="admin-page-head__actions">
        <a class="btn btn--ghost btn--sm" href="<?= url('/admin/categories') ?>"><?= icon('arrow-left', 15) ?> Back to categories</a>
    </div>
</div>

<div class="admin-grid admin-grid--2">
    <div class="panel">
        <div class="panel__head"><h2>Category details</h2></div>
        <div class="panel__body">
            <form method="post" action="<?= url('/admin/categories/' . $category['id']) ?>">
                <?= csrf_field() ?>

                <div class="form-grid">
                    <div class="field field--full">
                        <label class="field__label" for="name">Name <span class="req">*</span></label>
                        <input class="input <?= isset($errors['name']) ? 'input--error' : '' ?>" type="text" id="name" name="name"
                               value="<?= e(old('name', (string) $category['name'])) ?>" required>
                        <?php if (isset($errors['name'])): ?><span class="field__error"><?= e($errors['name']) ?></span><?php endif; ?>
                    </div>

                    <div class="field field--full">
                        <label class="field__label" for="slug">Slug</label>
                        <input class="input <?= isset($errors['slug']) ? 'input--error' : '' ?>" type="text" id="slug" name="slug"
                               value="<?= e(old('slug', (string) $category['slug'])) ?>">
                        <?php if (isset($errors['slug'])): ?><span class="field__error"><?= e($errors['slug']) ?></span><?php endif; ?>
                    </div>

                    <div class="field field--full">
                        <label class="field__label" for="description">Description</label>
                        <textarea class="textarea" id="description" name="description" rows="4"><?= e(old('description', (string) ($category['description'] ?? ''))) ?></textarea>
                    </div>

                    <div class="field">
                        <label class="field__label" for="icon">Icon</label>
                        <select class="select" id="icon" name="icon">
                            <?php foreach ($icons as $icon): ?>
                                <option value="<?= e($icon) ?>"<?= selected(old('icon', (string) $category['icon']), $icon) ?>><?= e(ucfirst($icon)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label class="field__label" for="sort_order">Sort order</label>
                        <input class="input" type="number" step="1" id="sort_order" name="sort_order"
                               value="<?= e(old('sort_order', (string) $category['sort_order'])) ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn btn--primary" type="submit"><?= icon('check', 17) ?> Save changes</button>
                    <a class="btn btn--ghost" href="<?= url('/admin/categories') ?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="account-main">
        <div class="panel">
            <div class="panel__head"><h2>Preview</h2></div>
            <div class="panel__body">
                <div class="category-card" style="pointer-events:none">
                    <span class="category-card__icon"><?= View::capture('partials/category-icon', ['icon' => $category['icon'], 'size' => 23]) ?></span>
                    <span class="category-card__name"><?= e($category['name']) ?></span>
                    <span class="category-card__count"><?= (int) $category['product_count'] ?> products</span>
                </div>
                <p class="small muted" style="margin-top:14px"><?= e($category['description'] ?: 'No description yet.') ?></p>
                <a class="btn btn--outline btn--block btn--sm" href="<?= e(url('/shop?category=' . urlencode((string) $category['slug']))) ?>" target="_blank" rel="noopener">
                    <?= icon('arrow-right', 15) ?> View in store
                </a>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head"><h2>Danger zone</h2></div>
            <div class="panel__body">
                <?php if ((int) $category['product_count'] > 0): ?>
                    <p class="small muted">This category still has <?= (int) $category['product_count'] ?> product<?= (int) $category['product_count'] === 1 ? '' : 's' ?>. Move them to another category before deleting.</p>
                    <button class="btn btn--danger btn--block" type="button" disabled>Delete category</button>
                <?php else: ?>
                    <form method="post" action="<?= url('/admin/categories/' . $category['id'] . '/delete') ?>"
                          data-confirm="Delete category &quot;<?= e($category['name']) ?>&quot;?">
                        <?= csrf_field() ?>
                        <button class="btn btn--danger btn--block" type="submit"><?= icon('trash', 16) ?> Delete category</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
