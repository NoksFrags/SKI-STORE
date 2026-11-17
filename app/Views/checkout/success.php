<?php

use App\Models\Order;

$payment = Order::PAYMENT_METHODS[$order['payment_method']] ?? ucfirst((string) $order['payment_method']);
?>
<section class="section section--tight">
    <div class="container">
        <div class="success-banner">
            <span class="success-banner__icon"><?= icon('check', 30) ?></span>
            <div>
                <span class="badge badge--success">Payment: <?= e($payment) ?></span>
                <h1>Thank you, <?= e(explode(' ', (string) $order['customer_name'])[0]) ?>!</h1>
                <p>Order <strong><?= e($order['order_number']) ?></strong> is in. We will email a confirmation to <?= e($order['email']) ?> and start packing right away.</p>
            </div>
        </div>

        <div class="cart-layout" style="margin-top:26px">
            <div class="account-main">
                <div class="panel">
                    <div class="panel__head">
                        <h2>Your items</h2>
                        <span class="badge <?= e(order_status_class((string) $order['status'])) ?>"><?= e(Order::statusLabel((string) $order['status'])) ?></span>
                    </div>
                    <div class="panel__body">
                        <?php \App\Core\View::partial('partials/order-lines', ['items' => $items]); ?>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel__head"><h2>Delivery details</h2></div>
                    <div class="panel__body">
                        <div class="info-grid">
                            <div><span>Recipient</span><strong><?= e($order['customer_name']) ?></strong></div>
                            <div><span>Phone</span><strong><?= e($order['phone']) ?></strong></div>
                            <div><span>Email</span><strong><?= e($order['email']) ?></strong></div>
                            <div>
                                <span>Address</span>
                                <strong><?= e($order['shipping_address']) ?><br><?= e($order['shipping_postal_code'] . ' ' . $order['shipping_city']) ?><br><?= e($order['shipping_country']) ?></strong>
                            </div>
                            <?php if (! empty($order['notes'])): ?>
                                <div><span>Notes</span><p><?= e($order['notes']) ?></p></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="summary-card">
                <h2>Order summary</h2>
                <div class="summary-row"><span>Order number</span><span><?= e($order['order_number']) ?></span></div>
                <div class="summary-row"><span>Placed</span><span><?= e(date('d M Y, H:i', strtotime((string) $order['created_at']))) ?></span></div>
                <div class="summary-row"><span>Payment</span><span><?= e($payment) ?></span></div>
                <div class="summary-row"><span>Subtotal</span><span><?= e(money((float) $order['subtotal'])) ?></span></div>
                <div class="summary-row <?= (float) $order['shipping_cost'] <= 0 ? 'summary-row--free' : '' ?>">
                    <span>Delivery</span>
                    <span><?= (float) $order['shipping_cost'] <= 0 ? 'Free' : e(money((float) $order['shipping_cost'])) ?></span>
                </div>
                <div class="summary-row summary-row--total"><span>Total</span><span><?= e(money((float) $order['total'])) ?></span></div>

                <a class="btn btn--dark btn--block" href="<?= url('/account/orders/' . $order['id']) ?>"><?= icon('package', 17) ?> View order</a>
                <a class="btn btn--outline btn--block" href="<?= url('/shop') ?>" style="margin-top:10px">Continue shopping</a>
            </aside>
        </div>
    </div>
</section>
