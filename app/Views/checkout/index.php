<?php

use App\Models\Order;

$errors = errors();
$userName = trim((string) ($user['full_name'] ?? ''));
$userEmail = trim((string) ($user['email'] ?? ''));
$userCountry = trim((string) ($user['country'] ?? '')) ?: 'Bosnia and Herzegovina';
?>
<section class="page-head page-head--compact">
    <div class="container page-head__inner">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= url('/') ?>">Home</a>
            <?= icon('chevron', 13) ?>
            <a href="<?= url('/cart') ?>">Cart</a>
            <?= icon('chevron', 13) ?>
            <span>Checkout</span>
        </nav>
        <h1>Checkout</h1>
        <p>Delivery details, payment and a last look at your order.</p>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <div class="checkout-layout">
            <form class="checkout-form" method="post" action="<?= url('/checkout') ?>">
                <?= csrf_field() ?>

                <div class="panel">
                    <div class="panel__head">
                        <h2><?= icon('user', 18) ?> Contact &amp; delivery</h2>
                        <span class="badge badge--muted">Step 1 of 2</span>
                    </div>

                    <div class="checkout-section">
                        <div class="form-grid">
                            <div class="field">
                                <label class="field__label" for="customer_name">Full name <span class="req">*</span></label>
                                <input class="input <?= isset($errors['customer_name']) ? 'input--error' : '' ?>" type="text" id="customer_name"
                                       name="customer_name" value="<?= e(old('customer_name', $userName)) ?>" autocomplete="name" required>
                                <?php if (isset($errors['customer_name'])): ?><span class="field__error"><?= e($errors['customer_name']) ?></span><?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field__label" for="email">Email <span class="req">*</span></label>
                                <input class="input <?= isset($errors['email']) ? 'input--error' : '' ?>" type="email" id="email"
                                       name="email" value="<?= e(old('email', $userEmail)) ?>" autocomplete="email" required>
                                <?php if (isset($errors['email'])): ?><span class="field__error"><?= e($errors['email']) ?></span><?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field__label" for="phone">Phone <span class="req">*</span></label>
                                <input class="input <?= isset($errors['phone']) ? 'input--error' : '' ?>" type="tel" id="phone"
                                       name="phone" value="<?= e(old('phone', (string) ($user['phone'] ?? ''))) ?>" autocomplete="tel" required>
                                <?php if (isset($errors['phone'])): ?><span class="field__error"><?= e($errors['phone']) ?></span><?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field__label" for="shipping_country">Country <span class="req">*</span></label>
                                <select class="select <?= isset($errors['shipping_country']) ? 'select--error' : '' ?>" id="shipping_country" name="shipping_country" required>
                                    <?php foreach (['Bosnia and Herzegovina', 'Croatia', 'Serbia', 'Montenegro', 'Slovenia'] as $country): ?>
                                        <option value="<?= e($country) ?>"<?= selected(old('shipping_country', $userCountry), $country) ?>><?= e($country) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['shipping_country'])): ?><span class="field__error"><?= e($errors['shipping_country']) ?></span><?php endif; ?>
                            </div>

                            <div class="field field--full">
                                <label class="field__label" for="shipping_address">Street address <span class="req">*</span></label>
                                <input class="input <?= isset($errors['shipping_address']) ? 'input--error' : '' ?>" type="text" id="shipping_address"
                                       name="shipping_address" value="<?= e(old('shipping_address', (string) ($user['address'] ?? ''))) ?>" autocomplete="street-address" required>
                                <?php if (isset($errors['shipping_address'])): ?><span class="field__error"><?= e($errors['shipping_address']) ?></span><?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field__label" for="shipping_city">City <span class="req">*</span></label>
                                <input class="input <?= isset($errors['shipping_city']) ? 'input--error' : '' ?>" type="text" id="shipping_city"
                                       name="shipping_city" value="<?= e(old('shipping_city', (string) ($user['city'] ?? ''))) ?>" autocomplete="address-level2" required>
                                <?php if (isset($errors['shipping_city'])): ?><span class="field__error"><?= e($errors['shipping_city']) ?></span><?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field__label" for="shipping_postal_code">Postal code <span class="req">*</span></label>
                                <input class="input <?= isset($errors['shipping_postal_code']) ? 'input--error' : '' ?>" type="text" id="shipping_postal_code"
                                       name="shipping_postal_code" value="<?= e(old('shipping_postal_code', (string) ($user['postal_code'] ?? ''))) ?>" autocomplete="postal-code" required>
                                <?php if (isset($errors['shipping_postal_code'])): ?><span class="field__error"><?= e($errors['shipping_postal_code']) ?></span><?php endif; ?>
                            </div>

                            <div class="field field--full">
                                <label class="field__label" for="notes">Delivery notes <span class="muted">(optional)</span></label>
                                <textarea class="textarea" id="notes" name="notes" rows="3"
                                          placeholder="Floor, entry code, best time to deliver&hellip;"><?= e(old('notes')) ?></textarea>
                                <?php if (isset($errors['notes'])): ?><span class="field__error"><?= e($errors['notes']) ?></span><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel__head">
                        <h2><?= icon('card', 18) ?> Payment</h2>
                        <span class="badge badge--muted">Step 2 of 2</span>
                    </div>
                    <div class="checkout-section">
                        <div class="payment-options">
                            <?php foreach (Order::PAYMENT_METHODS as $value => $label):
                                $description = match ($value) {
                                    'card'             => 'Pay with a card when the courier hands over your order.',
                                    'cash_on_delivery' => 'Pay the courier in cash when your parcel arrives.',
                                    default            => 'Receive our account details by email after placing the order.',
                                };
                                ?>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="<?= e($value) ?>"
                                        <?= old('payment_method', 'card') === $value ? 'checked' : '' ?>>
                                    <span class="payment-option__mark"></span>
                                    <span class="payment-option__body">
                                        <strong><?= e($label) ?></strong>
                                        <span><?= e($description) ?></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?php if (isset($errors['payment_method'])): ?>
                            <span class="field__error" style="display:block;margin-top:10px"><?= e($errors['payment_method']) ?></span>
                        <?php endif; ?>

                        <p class="summary-note" style="margin-top:16px">
                            <?= icon('lock', 15) ?> Your details are used only to process this order.
                        </p>
                    </div>
                </div>

                <div class="btn-row">
                    <button class="btn btn--primary btn--lg" type="submit"><?= icon('check', 18) ?> Place order &middot; <?= e(money($totals['total'])) ?></button>
                    <a class="btn btn--ghost" href="<?= url('/cart') ?>"><?= icon('arrow-left', 16) ?> Back to cart</a>
                </div>
            </form>

            <aside class="summary-card">
                <h2>Your order</h2>

                <?php foreach ($items as $item): ?>
                    <div class="summary-item">
                        <div class="summary-item__thumb">
                            <img src="<?= e(product_image($item['image'])) ?>" alt="<?= e($item['name']) ?>">
                        </div>
                        <div>
                            <div class="summary-item__name"><?= e($item['name']) ?></div>
                            <div class="summary-item__meta"><?= (int) $item['quantity'] ?> &times; <?= e(money((float) $item['price'])) ?></div>
                        </div>
                        <div class="summary-item__price"><?= e(money((float) $item['line_total'])) ?></div>
                    </div>
                <?php endforeach; ?>

                <div class="summary-row" style="margin-top:14px"><span>Subtotal</span><span><?= e(money($totals['subtotal'])) ?></span></div>
                <div class="summary-row <?= $totals['shipping'] <= 0 ? 'summary-row--free' : '' ?>">
                    <span>Delivery</span>
                    <span><?= $totals['shipping'] <= 0 ? 'Free' : e(money($totals['shipping'])) ?></span>
                </div>
                <div class="summary-row summary-row--total"><span>Total</span><span><?= e(money($totals['total'])) ?></span></div>

                <p class="summary-note"><?= icon('truck', 15) ?> Delivery in 2&ndash;4 working days across Bosnia and Herzegovina.</p>
            </aside>
        </div>
    </div>
</section>
