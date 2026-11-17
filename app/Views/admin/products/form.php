<?php

$isEdit = $product !== null;
$action = $isEdit ? url('/admin/products/' . $product['id']) : url('/admin/products');
$errors = errors();
$submitted = old('name') !== '' || old('slug') !== '' || isset($errors['name']);
$featured = $submitted ? old('is_featured') !== '' : (bool) ($product['is_featured'] ?? false);
$active = $submitted ? old('is_active') !== '' : (bool) ($product['is_active'] ?? true);
$currentImage = $product['image'] ?? null;
?>
<div class="admin-page-head">
    <div>
        <h1><?= $isEdit ? 'Edit product' : 'New product' ?></h1>
        <p><?= $isEdit ? e($product['name']) : 'Add a piece of gear to the catalogue.' ?></p>
    </div>
    <div class="admin-page-head__actions">
        <a class="btn btn--ghost btn--sm" href="<?= url('/admin/products') ?>"><?= icon('arrow-left', 15) ?> Back to products</a>
    </div>
</div>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="admin-form-grid">
        <div class="account-main">
            <div class="panel">
                <div class="panel__head"><h2>Details</h2></div>
                <div class="panel__body">
                    <div class="form-grid">
                        <div class="field field--full">
                            <label class="field__label" for="name">Product name <span class="req">*</span></label>
                            <input class="input <?= isset($errors['name']) ? 'input--error' : '' ?>" type="text" id="name" name="name"
                                   value="<?= e(old('name', (string) ($product['name'] ?? ''))) ?>" required autofocus>
                            <?php if (isset($errors['name'])): ?><span class="field__error"><?= e($errors['name']) ?></span><?php endif; ?>
                        </div>

                        <div class="field">
                            <label class="field__label" for="slug">Slug</label>
                            <input class="input <?= isset($errors['slug']) ? 'input--error' : '' ?>" type="text" id="slug" name="slug"
                                   value="<?= e(old('slug', (string) ($product['slug'] ?? ''))) ?>" placeholder="auto-generated from the name">
                            <?php if (isset($errors['slug'])): ?><span class="field__error"><?= e($errors['slug']) ?></span><?php endif; ?>
                        </div>

                        <div class="field">
                            <label class="field__label" for="category_id">Category</label>
                            <select class="select" id="category_id" name="category_id">
                                <option value="">Uncategorised</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int) $category['id'] ?>"<?= selected(old('category_id', (string) ($product['category_id'] ?? '')), (string) $category['id']) ?>><?= e($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['category_id'])): ?><span class="field__error"><?= e($errors['category_id']) ?></span><?php endif; ?>
                        </div>

                        <div class="field">
                            <label class="field__label" for="brand">Brand</label>
                            <input class="input" type="text" id="brand" name="brand" value="<?= e(old('brand', (string) ($product['brand'] ?? ''))) ?>">
                        </div>

                        <div class="field">
                            <label class="field__label" for="sku">SKU</label>
                            <input class="input" type="text" id="sku" name="sku" value="<?= e(old('sku', (string) ($product['sku'] ?? ''))) ?>" placeholder="NV-ATL-172">
                        </div>

                        <div class="field field--full">
                            <label class="field__label" for="short_description">Short description</label>
                            <input class="input" type="text" id="short_description" name="short_description" maxlength="255"
                                   value="<?= e(old('short_description', (string) ($product['short_description'] ?? ''))) ?>">
                            <span class="field__hint">Shown on product cards and in listings. Keep it under 160 characters.</span>
                        </div>

                        <div class="field field--full">
                            <label class="field__label" for="description">Full description</label>
                            <textarea class="textarea" id="description" name="description" rows="7"><?= e(old('description', (string) ($product['description'] ?? ''))) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel__head"><h2>Pricing &amp; stock</h2></div>
                <div class="panel__body">
                    <div class="form-grid">
                        <div class="field">
                            <label class="field__label" for="price">Price <span class="req">*</span></label>
                            <div class="input-group">
                                <input class="input <?= isset($errors['price']) ? 'input--error' : '' ?>" type="number" step="0.01" min="0" id="price" name="price"
                                       value="<?= e(old('price', (string) ($product['price'] ?? ''))) ?>" required>
                                <span class="input-group__addon"><?= e(config('app.currency')) ?></span>
                            </div>
                            <?php if (isset($errors['price'])): ?><span class="field__error"><?= e($errors['price']) ?></span><?php endif; ?>
                        </div>

                        <div class="field">
                            <label class="field__label" for="compare_at_price">Compare-at price</label>
                            <div class="input-group">
                                <input class="input <?= isset($errors['compare_at_price']) ? 'input--error' : '' ?>" type="number" step="0.01" min="0" id="compare_at_price" name="compare_at_price"
                                       value="<?= e(old('compare_at_price', ($product['compare_at_price'] ?? null) !== null ? (string) $product['compare_at_price'] : '')) ?>">
                                <span class="input-group__addon"><?= e(config('app.currency')) ?></span>
                            </div>
                            <span class="field__hint">Leave empty for no sale badge.</span>
                            <?php if (isset($errors['compare_at_price'])): ?><span class="field__error"><?= e($errors['compare_at_price']) ?></span><?php endif; ?>
                        </div>

                        <div class="field">
                            <label class="field__label" for="stock">Stock quantity <span class="req">*</span></label>
                            <input class="input <?= isset($errors['stock']) ? 'input--error' : '' ?>" type="number" step="1" min="0" id="stock" name="stock"
                                   value="<?= e(old('stock', (string) ($product['stock'] ?? '0'))) ?>" required>
                            <?php if (isset($errors['stock'])): ?><span class="field__error"><?= e($errors['stock']) ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="toggle-row" style="margin-top:12px">
                        <label class="checkbox">
                            <input type="checkbox" name="is_featured" value="1" <?= $featured ? 'checked' : '' ?>>
                            <span><strong>Featured product</strong><br><span class="muted small">Show on the home page and highlight in listings.</span></span>
                        </label>
                    </div>
                    <div class="toggle-row">
                        <label class="checkbox">
                            <input type="checkbox" name="is_active" value="1" <?= $active ? 'checked' : '' ?>>
                            <span><strong>Visible in the store</strong><br><span class="muted small">Uncheck to hide without deleting.</span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="account-main">
            <div class="panel">
                <div class="panel__head"><h2>Product image</h2></div>
                <div class="panel__body">
                    <div class="image-preview">
                        <img src="<?= e(product_image($currentImage)) ?>" alt="Current product image">
                        <p><?= $currentImage ? 'Current image. Upload a new file to replace it.' : 'No image uploaded - the placeholder is used.' ?></p>
                    </div>

                    <div class="field" style="margin-top:16px">
                        <label class="field__label" for="image">Upload image</label>
                        <input class="input" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                        <span class="field__hint">JPG, PNG or WebP, up to 3 MB. Landscape 4:3 works best.</span>
                        <?php if (isset($errors['image'])): ?><span class="field__error"><?= e($errors['image']) ?></span><?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel__body">
                    <button class="btn btn--primary btn--block" type="submit">
                        <?= icon('check', 17) ?> <?= $isEdit ? 'Save changes' : 'Create product' ?>
                    </button>
                    <a class="btn btn--ghost btn--block" href="<?= url('/admin/products') ?>" style="margin-top:8px">Cancel</a>

                    <?php if ($isEdit): ?>
                        <div class="account-nav__sep" style="margin:18px 0"></div>
                        <p class="small muted" style="margin-bottom:10px">Danger zone</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($isEdit): ?>
                <form method="post" action="<?= url('/admin/products/' . $product['id'] . '/delete') ?>"
                      data-confirm="Delete &quot;<?= e($product['name']) ?>&quot;? This cannot be undone.">
                    <?= csrf_field() ?>
                    <button class="btn btn--danger btn--block" type="submit"><?= icon('trash', 16) ?> Delete product</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</form>
