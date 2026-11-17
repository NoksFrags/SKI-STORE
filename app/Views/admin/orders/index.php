<?php

use App\Core\View;
use App\Models\Order;
?>
<div class="admin-page-head">
    <div>
        <h1>Orders</h1>
        <p><?= (int) $total ?> order<?= (int) $total === 1 ? '' : 's' ?><?= $status !== '' ? ' with status ' . e(Order::statusLabel($status)) : '' ?>.</p>
    </div>
</div>

<form class="admin-filterbar" method="get" action="<?= url('/admin/orders') ?>">
    <input class="input" type="search" name="q" value="<?= e($search) ?>" placeholder="Order number, customer, email&hellip;" aria-label="Search orders">
    <select class="select" name="status" aria-label="Filter by status">
        <option value="">All statuses</option>
        <?php foreach ($statuses as $value => $label): ?>
            <option value="<?= e($value) ?>"<?= selected($status, $value) ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn--outline btn--sm" type="submit"><?= icon('filter', 15) ?> Filter</button>
    <?php if ($search !== '' || $status !== ''): ?>
        <a class="btn btn--ghost btn--sm" href="<?= url('/admin/orders') ?>">Reset</a>
    <?php endif; ?>
</form>

<div class="panel">
    <div class="panel__body panel__body--flush">
        <?php if ($orders === []): ?>
            <div class="empty" style="border:0;border-radius:0;padding:44px 20px">
                <span class="empty__icon"><?= icon('receipt', 26) ?></span>
                <div class="empty__title">No orders match</div>
                <p class="empty__text">Try another search term or clear the status filter.</p>
                <a class="btn btn--outline btn--sm" href="<?= url('/admin/orders') ?>">Clear filters</a>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th>Order</th><th>Customer</th><th>Placed</th><th>Payment</th><th>Total</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong><?= e($order['order_number']) ?></strong></td>
                                <td>
                                    <div class="table__name"><?= e($order['customer_name']) ?></div>
                                    <div class="table__sub"><?= e($order['email']) ?></div>
                                </td>
                                <td class="muted"><?= e(date('d M Y', strtotime((string) $order['created_at']))) ?></td>
                                <td class="muted"><?= e(Order::PAYMENT_METHODS[$order['payment_method']] ?? $order['payment_method']) ?></td>
                                <td class="num"><strong><?= e(money((float) $order['total'])) ?></strong></td>
                                <td><span class="badge <?= e(order_status_class((string) $order['status'])) ?>"><?= e(Order::statusLabel((string) $order['status'])) ?></span></td>
                                <td style="text-align:right">
                                    <a class="btn btn--outline btn--sm" href="<?= url('/admin/orders/' . $order['id']) ?>">Open</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($orders !== []): ?>
    <?php View::partial('partials/pagination', ['page' => $page, 'pages' => $pages]); ?>
<?php endif; ?>
