<?php

use App\Core\Auth;

$stock = (int) ($product['stock'] ?? 0);
$compare = isset($product['compare_at_price']) && $product['compare_at_price'] !== null ? (float) $product['compare_at_price'] : 0.0;
$price = (float) ($product['price'] ?? 0);
$onSale = $compare > $price;
$discount = $onSale ? (int) round((1 - $price / $compare) * 100) : 0;
$saved = in_wishlist((int) $product['id']);
?>
<article class="product-card">
    <a class="product-card__media" href="<?= e(url('/product/' . $product['slug'])) ?>" tabindex="-1" aria-hidden="true">
        <img src="<?= e(product_image($product['image'] ?? null)) ?>" alt="<?= e($product['name']) ?>" loading="lazy" width="800" height="600">
        <div class="product-card__badges">
            <?php if ($stock < 1): ?>
                <span class="badge badge--muted">Sold out</span>
            <?php elseif ($onSale): ?>
                <span class="badge badge--sale">&minus;<?= $discount ?>%</span>
            <?php endif; ?>
        </div>
    </a>

    <?php if (Auth::check()): ?>
        <form method="post" action="<?= url('/wishlist/toggle') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <button class="product-card__wish <?= $saved ? 'is-active' : '' ?>" type="submit"
                    aria-label="<?= $saved ? 'Remove from wishlist' : 'Save to wishlist' ?>" aria-pressed="<?= $saved ? 'true' : 'false' ?>">
                <?= icon('heart', 18) ?>
            </button>
        </form>
    <?php else: ?>
        <a class="product-card__wish" href="<?= url('/login') ?>" aria-label="Sign in to save this item">
            <?= icon('heart', 18) ?>
        </a>
    <?php endif; ?>

    <div class="product-card__body">
        <span class="product-card__brand"><?= e($product['brand'] ?: 'Ski Store') ?></span>
        <h3 class="product-card__title">
            <a href="<?= e(url('/product/' . $product['slug'])) ?>"><?= e($product['name']) ?></a>
        </h3>
        <p class="product-card__desc"><?= e($product['short_description'] ?? '') ?></p>

        <div class="product-card__price">
            <span class="price-now"><?= e(money($price)) ?></span>
            <?php if ($onSale): ?>
                <span class="price-was"><?= e(money($compare)) ?></span>
            <?php endif; ?>
        </div>

        <div class="product-card__foot">
            <?php if ($stock < 1): ?>
                <span class="product-card__stock product-card__stock--out">Out of stock</span>
            <?php elseif ($stock <= 5): ?>
                <span class="product-card__stock product-card__stock--low">Only <?= $stock ?> left</span>
            <?php endif; ?>

            <form method="post" action="<?= url('/cart/add') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                <input type="hidden" name="quantity" value="1">
                <button class="product-card__add" type="submit" <?= $stock < 1 ? 'disabled' : '' ?>
                        aria-label="Add <?= e($product['name']) ?> to cart">
                    <?= icon($stock < 1 ? 'close' : 'plus', 18) ?>
                </button>
            </form>
        </div>
    </div>
</article>
