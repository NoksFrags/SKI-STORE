<?php

use App\Core\Auth;
use App\Core\View;

$stock = (int) $product['stock'];
$price = (float) $product['price'];
$compare = $product['compare_at_price'] !== null ? (float) $product['compare_at_price'] : 0.0;
$onSale = $compare > $price;
$saved = in_wishlist((int) $product['id']);
$maxQty = max(1, $stock);
?>
<section class="page-head page-head--compact">
    <div class="container page-head__inner">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= url('/') ?>">Home</a>
            <?= icon('chevron', 13) ?>
            <a href="<?= url('/shop') ?>">Shop</a>
            <?php if (! empty($product['category_slug'])): ?>
                <?= icon('chevron', 13) ?>
                <a href="<?= e(url('/shop?category=' . urlencode((string) $product['category_slug']))) ?>"><?= e($product['category_name']) ?></a>
            <?php endif; ?>
            <?= icon('chevron', 13) ?>
            <span><?= e($product['name']) ?></span>
        </nav>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <div class="product-detail">
            <div class="product-gallery">
                <div class="product-gallery__main">
                    <img src="<?= e(product_image($product['image'])) ?>" alt="<?= e($product['name']) ?>" width="800" height="600">
                </div>
                <p class="product-gallery__note">
                    <?= icon('info', 16) ?> Studio illustration of the <?= e($product['name']) ?>. Colourways may vary.
                </p>
            </div>

            <div class="product-info">
                <div class="product-info__brand"><?= e($product['brand'] ?: 'Ski Store') ?></div>
                <h1><?= e($product['name']) ?></h1>

                <div class="product-info__price">
                    <span class="price-now"><?= e(money($price)) ?></span>
                    <?php if ($onSale): ?>
                        <span class="price-was"><?= e(money($compare)) ?></span>
                        <span class="price-off">Save <?= e(money($compare - $price)) ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($stock < 1): ?>
                    <p class="stock-line stock-line--out"><span class="stock-dot"></span> Out of stock &middot; ask us about the next delivery</p>
                <?php elseif ($stock <= 5): ?>
                    <p class="stock-line stock-line--low"><span class="stock-dot"></span> Low stock &middot; only <?= $stock ?> left</p>
                <?php else: ?>
                    <p class="stock-line stock-line--in"><span class="stock-dot"></span> In stock &middot; ships in 2&ndash;4 working days</p>
                <?php endif; ?>

                <p class="product-info__desc"><?= e($product['short_description'] ?? '') ?></p>

                <?php if ($stock > 0): ?>
                    <form class="product-actions" method="post" action="<?= url('/cart/add') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">

                        <div class="qty" data-qty>
                            <button class="qty__btn" type="button" data-step="-1" aria-label="Decrease quantity"><?= icon('minus', 16) ?></button>
                            <input class="qty__input" type="number" name="quantity" value="1" min="1" max="<?= $maxQty ?>" aria-label="Quantity">
                            <button class="qty__btn" type="button" data-step="1" aria-label="Increase quantity"><?= icon('plus', 16) ?></button>
                        </div>

                        <button class="btn btn--primary btn--lg" type="submit"><?= icon('cart', 19) ?> Add to cart</button>
                    </form>
                <?php else: ?>
                    <div class="product-actions">
                        <button class="btn btn--outline btn--lg" type="button" disabled>Out of stock</button>
                        <a class="btn btn--ghost" href="tel:+38733555100"><?= icon('phone', 17) ?> Ask about restock</a>
                    </div>
                <?php endif; ?>

                <?php if (Auth::check()): ?>
                    <form method="post" action="<?= url('/wishlist/toggle') ?>" style="margin-top:12px">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <button class="wish-btn <?= $saved ? 'is-active' : '' ?>" type="submit" style="width:auto;padding:0 16px;gap:8px;display:inline-flex;align-items:center"
                                aria-pressed="<?= $saved ? 'true' : 'false' ?>">
                            <?= icon('heart', 18) ?>
                            <span><?= $saved ? 'Saved to wishlist' : 'Save to wishlist' ?></span>
                        </button>
                    </form>
                <?php else: ?>
                    <p class="small muted" style="margin-top:12px">
                        <a class="section__link" href="<?= url('/login') ?>">Sign in</a> to save this item to your wishlist.
                    </p>
                <?php endif; ?>

                <div class="buy-notes">
                    <div class="buy-note">
                        <?= icon('truck', 19) ?>
                        <div><strong>Free delivery over <?= e(money((float) config('app.free_shipping_from'))) ?></strong>
                            Flat <?= e(money((float) config('app.shipping_flat'))) ?> below that, 2&ndash;4 working days.</div>
                    </div>
                    <div class="buy-note">
                        <?= icon('shield', 19) ?>
                        <div><strong>30-day returns</strong>
                            Unused and in original packaging. We cover defects for two seasons.</div>
                    </div>
                    <div class="buy-note">
                        <?= icon('users', 19) ?>
                        <div><strong>Free fitting advice</strong>
                            Call <?= e('+387 33 555 100') ?> or visit the Sarajevo workshop.</div>
                    </div>
                </div>

                <dl class="meta-list">
                    <div><dt>SKU</dt><dd class="mono"><?= e($product['sku'] ?: '—') ?></dd></div>
                    <div><dt>Category</dt>
                        <dd>
                            <?php if (! empty($product['category_slug'])): ?>
                                <a href="<?= e(url('/shop?category=' . urlencode((string) $product['category_slug']))) ?>"><?= e($product['category_name']) ?></a>
                            <?php else: ?>—<?php endif; ?>
                        </dd>
                    </div>
                </dl>

                <details class="details">
                    <summary>Full description</summary>
                    <div class="details__body">
                        <p><?= e($product['description'] ?: ($product['short_description'] ?? '')) ?></p>
                    </div>
                </details>

                <details class="details">
                    <summary>Delivery &amp; returns</summary>
                    <div class="details__body">
                        <p>Orders placed before 14:00 ship the same working day. Delivery within Bosnia and Herzegovina takes 2&ndash;4 working days. Free above <?= e(money((float) config('app.free_shipping_from'))) ?>, otherwise <?= e(money((float) config('app.shipping_flat'))) ?>.
If something does not fit, return it unused within 30 days for a full refund.</p>
                    </div>
                </details>
            </div>
        </div>

        <?php if ($related !== []): ?>
            <div class="section">
                <div class="section__head">
                    <div>
                        <span class="eyebrow">Pairs well with</span>
                        <h2>Complete the setup</h2>
                    </div>
                    <a class="section__link" href="<?= url('/shop') ?>">Shop all <?= icon('arrow-right', 15) ?></a>
                </div>
                <div class="product-grid product-grid--three">
                    <?php foreach ($related as $item): ?>
                        <?php View::partial('partials/product-card', ['product' => $item]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
