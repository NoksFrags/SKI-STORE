<?php

$freeFrom = (float) $totals['free_shipping_from'];
$progress = $freeFrom > 0 ? min(100, ($totals['subtotal'] / $freeFrom) * 100) : 100;
?>
<section class="page-head page-head--compact">
    <div class="container page-head__inner">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= url('/') ?>">Home</a>
            <?= icon('chevron', 13) ?>
            <span>Shopping cart</span>
        </nav>
        <h1>Your cart</h1>
        <p><?= $items === [] ? 'Nothing in the basket yet.' : $totals['count'] . ' item' . ($totals['count'] === 1 ? '' : 's') . ' ready for checkout.' ?></p>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <?php if ($items === []): ?>
            <div class="empty">
                <span class="empty__icon"><?= icon('cart', 28) ?></span>
                <div class="empty__title">Your cart is empty</div>
                <p class="empty__text">Start with the gear you actually need: a solid all-mountain setup, warm layers and goggles that work in flat light.</p>
                <div class="btn-row">
                    <a class="btn btn--primary" href="<?= url('/shop') ?>">Shop all gear</a>
                    <a class="btn btn--outline" href="<?= url('/wishlist') ?>">View wishlist</a>
                </div>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                <div class="cart-panel">
                    <div class="cart-head">
                        <h2>Items in your cart</h2>
                        <a class="section__link" href="<?= url('/shop') ?>">Continue shopping</a>
                    </div>

                    <form method="post" action="<?= url('/cart/update') ?>" data-auto-submit>
                        <?= csrf_field() ?>
                        <div class="cart-list">
                            <?php foreach ($items as $item): ?>
                                <?php $stock = (int) $item['stock']; ?>
                                <div class="cart-row">
                                    <a class="cart-row__media" href="<?= e(url('/product/' . $item['slug'])) ?>">
                                        <img src="<?= e(product_image($item['image'])) ?>" alt="<?= e($item['name']) ?>" loading="lazy">
                                    </a>

                                    <div>
                                        <div class="cart-row__title"><a href="<?= e(url('/product/' . $item['slug'])) ?>"><?= e($item['name']) ?></a></div>
                                        <div class="cart-row__meta">
                                            <?= e($item['brand'] ?: 'Ski Store') ?> &middot; <?= e(money((float) $item['price'])) ?> each
                                            <?php if ($stock < 1): ?> &middot; <span class="badge badge--danger">Out of stock</span><?php endif; ?>
                                        </div>
                                        <div class="cart-row__controls">
                                            <div class="qty" data-qty>
                                                <button class="qty__btn" type="button" data-step="-1" aria-label="Decrease quantity"><?= icon('minus', 15) ?></button>
                                                <input class="qty__input" type="number" name="quantities[<?= (int) $item['product_id'] ?>]"
                                                       value="<?= (int) $item['quantity'] ?>" min="1" max="<?= max(1, $stock) ?>"
                                                       aria-label="Quantity for <?= e($item['name']) ?>">
                                                <button class="qty__btn" type="button" data-step="1" aria-label="Increase quantity"><?= icon('plus', 15) ?></button>
                                            </div>
                                            <button class="cart-row__remove" type="submit" form="remove-<?= (int) $item['product_id'] ?>">
                                                <?= icon('trash', 15) ?> Remove
                                            </button>
                                        </div>
                                    </div>

                                    <div class="cart-row__price">
                                        <strong><?= e(money((float) $item['line_total'])) ?></strong>
                                        <span><?= (int) $item['quantity'] ?> &times; <?= e(money((float) $item['price'])) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="cart-actions">
                            <button class="btn btn--outline btn--sm" type="submit"><?= icon('check', 16) ?> Update quantities</button>
                            <span class="small muted">Quantities update automatically when changed.</span>
                        </div>
                    </form>
                </div>

                <aside class="summary-card">
                    <h2>Order summary</h2>

                    <div class="progress">
                        <div class="progress__track">
                            <div class="progress__bar" style="width: <?= number_format($progress, 1, '.', '') ?>%"></div>
                        </div>
                        <p class="progress__label">
                            <?php if ($totals['free_shipping_gap'] > 0): ?>
                                Add <?= e(money($totals['free_shipping_gap'])) ?> more for free delivery.
                            <?php else: ?>
                                <strong style="color:var(--success)">Free delivery unlocked.</strong>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="summary-row"><span>Subtotal</span><span><?= e(money($totals['subtotal'])) ?></span></div>
                    <div class="summary-row <?= $totals['shipping'] <= 0 ? 'summary-row--free' : '' ?>">
                        <span>Delivery</span>
                        <span><?= $totals['shipping'] <= 0 ? 'Free' : e(money($totals['shipping'])) ?></span>
                    </div>
                    <div class="summary-row summary-row--muted"><span>VAT included</span><span>&mdash;</span></div>
                    <div class="summary-row summary-row--total"><span>Total</span><span><?= e(money($totals['total'])) ?></span></div>

                    <a class="btn btn--primary btn--block" href="<?= url('/checkout') ?>">
                        <?= icon('lock', 17) ?> Proceed to checkout
                    </a>

                    <p class="summary-note"><?= icon('shield', 15) ?> Secure checkout. Card on delivery, cash or bank transfer.</p>

                    <form method="post" action="<?= url('/cart/clear') ?>" data-confirm="Remove every item from your cart?" style="margin-top:14px">
                        <?= csrf_field() ?>
                        <button class="btn btn--ghost btn--sm btn--block" type="submit">Empty cart</button>
                    </form>
                </aside>
            </div>

            <?php foreach ($items as $item): ?>
                <form id="remove-<?= (int) $item['product_id'] ?>" method="post" action="<?= url('/cart/remove') ?>" hidden>
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                </form>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
