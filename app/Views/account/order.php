<?php

use App\Core\View;
use App\Models\Order;

$payment = Order::PAYMENT_METHODS[$order['payment_method']] ?? ucfirst((string) $order['payment_method']);
$canCancel = $order['status'] === 'pending';
?>
<section class="section section--tight">
    <div class="container">
        <div class="account-layout">
            <?php View::partial('partials/account-nav'); ?>

            <div class="account-main">
                <nav class="breadcrumb" style="color:var(--muted);margin-bottom:4px" aria-label="Breadcrumb">
                    <a href="<?= url('/account') ?>" style="color:var(--muted)">Account</a>
                    <?= icon('chevron', 13) ?>
                    <a href="<?= url('/account/orders') ?>" style="color:var(--muted)">Orders</a>
                    <?= icon('chevron', 13) ?>
                    <span><?= e($order['order_number']) ?></span>
                </nav>

                <div class="account-intro between">
                    <div>
                        <h1>Order <?= e($order['order_number']) ?></h1>
                        <p>Placed <?= e(date('d M Y, H:i', strtotime((string) $order['created_at']))) ?></p>
                    </div>
                    <span class="badge <?= e(order_status_class((string) $order['status'])) ?>"><?= e(Order::statusLabel((string) $order['status'])) ?></span>
                </div>

                <div class="cart-layout cart-layout--narrow">
                    <div class="account-main">
                        <div class="panel">
                            <div class="panel__head"><h2>Items</h2><span class="small muted"><?= count($items) ?> line<?= count($items) === 1 ? '' : 's' ?></span></div>
                            <div class="panel__body">
                                <?php View::partial('partials/order-lines', ['items' => $items]); ?>
                            </div>
                        </div>

                        <div class="panel">
                            <div class="panel__head"><h2>Delivery</h2></div>
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
                        <h2>Summary</h2>
                        <div class="summary-row"><span>Payment method</span><span><?= e($payment) ?></span></div>
                        <div class="summary-row"><span>Subtotal</span><span><?= e(money((float) $order['subtotal'])) ?></span></div>
                        <div class="summary-row <?= (float) $order['shipping_cost'] <= 0 ? 'summary-row--free' : '' ?>">
                            <span>Delivery</span>
                            <span><?= (float) $order['shipping_cost'] <= 0 ? 'Free' : e(money((float) $order['shipping_cost'])) ?></span>
                        </div>
                        <div class="summary-row summary-row--total"><span>Total</span><span><?= e(money((float) $order['total'])) ?></span></div>

                        <?php if ($canCancel): ?>
                            <form method="post" action="<?= url('/account/orders/' . $order['id'] . '/cancel') ?>"
                                  data-confirm="Cancel this order? Reserved stock will be released.">
                                <?= csrf_field() ?>
                                <button class="btn btn--danger btn--block" type="submit" style="margin-top:16px">Cancel order</button>
                            </form>
                            <p class="summary-note">Pending orders can be cancelled online. After packing starts, call us on +387 33 555 100.</p>
                        <?php else: ?>
                            <p class="summary-note" style="margin-top:14px"><?= icon('info', 15) ?> Need a change? Call +387 33 555 100 with your order number.</p>
                        <?php endif; ?>

                        <a class="btn btn--outline btn--block" href="<?= url('/account/orders') ?>" style="margin-top:10px">All orders</a>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</section>
