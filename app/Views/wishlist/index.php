<?php

use App\Core\View;
?>
<section class="page-head page-head--compact">
    <div class="container page-head__inner">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= url('/') ?>">Home</a>
            <?= icon('chevron', 13) ?>
            <a href="<?= url('/account') ?>">Account</a>
            <?= icon('chevron', 13) ?>
            <span>Wishlist</span>
        </nav>
        <h1>My wishlist</h1>
        <p><?= $items === [] ? 'Nothing saved yet.' : count($items) . ' saved item' . (count($items) === 1 ? '' : 's') . ' - keep an eye on stock and prices.' ?></p>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <?php if ($items === []): ?>
            <div class="empty">
                <span class="empty__icon"><?= icon('heart', 28) ?></span>
                <div class="empty__title">No saved gear yet</div>
                <p class="empty__text">Tap the heart on any product to keep it here while you compare lengths, flex and price.</p>
                <a class="btn btn--primary" href="<?= url('/shop') ?>">Browse the collection</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($items as $product): ?>
                    <div class="wishlist-item">
                        <?php View::partial('partials/product-card', ['product' => $product]); ?>
                        <div class="wishlist-item__actions">
                            <form method="post" action="<?= url('/cart/add') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button class="btn btn--dark btn--sm btn--block" type="submit" <?= (int) $product['stock'] < 1 ? 'disabled' : '' ?>>
                                    <?= icon('cart', 15) ?> <?= (int) $product['stock'] < 1 ? 'Out of stock' : 'Add to cart' ?>
                                </button>
                            </form>
                            <form method="post" action="<?= url('/wishlist/remove') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                <button class="btn btn--ghost btn--sm btn--block" type="submit"><?= icon('trash', 15) ?> Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
