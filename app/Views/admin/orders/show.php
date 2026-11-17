<?php

use App\Core\View;
use App\Models\Order;

$payment = Order::PAYMENT_METHODS[$order['payment_method']] ?? ucfirst((string) $order['payment_method']);
?>
<div class="admin-page-head">
    <div>
        <h1>Order <?= e($order['order_number']) ?></h1>
        <p>Placed <?= e(date('d M Y, H:i', strtotime((string) $order['created_at']))) ?> &middot; <?= e($payment) ?></p>
    </div>
    <div class="admin-page-head__actions">
        <span class="badge <?= e(order_status_class((string) $order['status'])) ?>"><?= e(Order::statusLabel((string) $order['status'])) ?></span>
        <a class="btn btn--ghost btn--sm" href="<?= url('/admin/orders') ?>"><?= icon('arrow-left', 15) ?> All orders</a>
    </div>
</div>

<div class="admin-grid admin-grid--2">
    <div class="account-main">
        <div class="panel">
            <div class="panel__head">
                <h2>Items</h2>
                <span class="small muted"><?= count($items) ?> line<?= count($items) === 1 ? '' : 's' ?></span>
            </div>
            <div class="panel__body">
                <?php View::partial('partials/order-lines', ['items' => $items]); ?>
            </div>
            <div class="panel__foot">
                <div class="summary-row"><span>Subtotal</span><span><?= e(money((float) $order['subtotal'])) ?></span></div>
                <div class="summary-row"><span>Delivery</span><span><?= (float) $order['shipping_cost'] <= 0 ? 'Free' : e(money((float) $order['shipping_cost'])) ?></span></div>
                <div class="summary-row summary-row--total"><span>Total</span><span><?= e(money((float) $order['total'])) ?></span></div>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head"><h2>Customer &amp; delivery</h2></div>
            <div class="panel__body">
                <div class="info-grid">
                    <div><span>Customer</span><strong><?= e($order['customer_name']) ?></strong></div>
                    <div><span>Account</span><strong><?= e($order['account_email'] ?? 'Guest / deleted account') ?></strong></div>
                    <div><span>Email</span><strong><?= e($order['email']) ?></strong></div>
                    <div><span>Phone</span><strong><?= e($order['phone']) ?></strong></div>
                    <div>
                        <span>Shipping address</span>
                        <strong><?= e($order['shipping_address']) ?><br><?= e($order['shipping_postal_code'] . ' ' . $order['shipping_city']) ?><br><?= e($order['shipping_country']) ?></strong>
                    </div>
                    <div><span>Notes</span><p><?= e($order['notes'] ?: 'No notes from the customer.') ?></p></div>
                </div>
            </div>
        </div>
    </div>

    <div class="account-main">
        <div class="panel">
            <div class="panel__head"><h2>Update status</h2></div>
            <div class="panel__body">
                <form method="post" action="<?= url('/admin/orders/' . $order['id'] . '/status') ?>">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label class="field__label" for="status">Order status</label>
                        <select class="select" id="status" name="status">
                            <?php foreach ($statuses as $value => $label): ?>
                                <option value="<?= e($value) ?>"<?= selected($order['status'], $value) ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field__hint">Cancelling an order returns reserved stock to the shelf.</span>
                    </div>
                    <button class="btn btn--primary btn--block" type="submit" style="margin-top:14px"><?= icon('check', 16) ?> Save status</button>
                </form>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head"><h2>Timeline</h2></div>
            <div class="panel__body">
                <div class="profile-list" style="grid-template-columns:1fr">
                    <div class="profile-list__item"><span>Order placed</span><strong><?= e(date('d M Y, H:i', strtotime((string) $order['created_at']))) ?></strong></div>
                    <div class="profile-list__item"><span>Last update</span><strong><?= e(date('d M Y, H:i', strtotime((string) $order['updated_at']))) ?></strong></div>
                    <div class="profile-list__item"><span>Payment</span><strong><?= e($payment) ?></strong></div>
                    <div class="profile-list__item"><span>Order number</span><strong><?= e($order['order_number']) ?></strong></div>
                </div>
            </div>
        </div>
    </div>
</div>
