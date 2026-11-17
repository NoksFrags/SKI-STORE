<?php

use App\Models\Order;

$orderStats = $orders;
$maxRevenue = 0.0;

foreach ($revenue as $month) {
    $maxRevenue = max($maxRevenue, (float) $month['revenue']);
}

$pipeline = [
    'pending'    => ['label' => 'Pending', 'count' => $orderStats['pending'], 'class' => 'badge--warning'],
    'processing' => ['label' => 'Processing', 'count' => $orderStats['processing'], 'class' => 'badge--info'],
    'shipped'    => ['label' => 'Shipped', 'count' => $orderStats['shipped'], 'class' => 'badge--brand'],
    'completed'  => ['label' => 'Completed', 'count' => $orderStats['completed'], 'class' => 'badge--success'],
];
?>
<div class="admin-page-head">
    <div>
        <h1>Store overview</h1>
        <p>Revenue, stock and orders at a glance &middot; <?= e(date('l, d F Y')) ?></p>
    </div>
    <div class="admin-page-head__actions">
        <a class="btn btn--outline btn--sm" href="<?= url('/admin/orders') ?>"><?= icon('receipt', 16) ?> All orders</a>
        <a class="btn btn--primary btn--sm" href="<?= url('/admin/products/create') ?>"><?= icon('plus', 16) ?> New product</a>
    </div>
</div>

<div class="admin-stats">
    <div class="stat-card stat-card--success">
        <div class="stat-card__label">Revenue (paid)</div>
        <div class="stat-card__value" style="font-size:1.45rem"><?= e(money($orderStats['revenue'])) ?></div>
        <div class="stat-card__hint"><?= e(money($orderStats['revenue_30d'])) ?> in the last 30 days</div>
        <span class="stat-card__icon"><?= icon('card', 18) ?></span>
    </div>
    <div class="stat-card">
        <div class="stat-card__label">Orders</div>
        <div class="stat-card__value"><?= (int) $orderStats['total_orders'] ?></div>
        <div class="stat-card__hint"><?= (int) $orderStats['pending'] ?> awaiting action</div>
        <span class="stat-card__icon"><?= icon('receipt', 18) ?></span>
    </div>
    <div class="stat-card stat-card--info">
        <div class="stat-card__label">Customers</div>
        <div class="stat-card__value"><?= (int) $customers ?></div>
        <div class="stat-card__hint"><?= (int) $newCustomers ?> new this month</div>
        <span class="stat-card__icon"><?= icon('users', 18) ?></span>
    </div>
    <div class="stat-card stat-card--accent">
        <div class="stat-card__label">Average order</div>
        <div class="stat-card__value" style="font-size:1.45rem"><?= e(money($orderStats['avg_order'])) ?></div>
        <div class="stat-card__hint"><?= (int) $products['active'] ?> active products</div>
        <span class="stat-card__icon"><?= icon('tag', 18) ?></span>
    </div>
</div>

<div class="admin-grid admin-grid--2">
    <div class="panel">
        <div class="panel__head">
            <h2>Revenue, last 6 months</h2>
            <span class="small muted">Excludes cancelled orders</span>
        </div>
        <div class="panel__body">
            <div class="chart">
                <div class="chart__bars">
                    <?php foreach ($revenue as $month): ?>
                        <?php $height = $maxRevenue > 0 ? max(3, (int) round(((float) $month['revenue'] / $maxRevenue) * 100)) : 3; ?>
                        <div class="chart__bar">
                            <span class="chart__value"><?= $month['revenue'] > 0 ? e(number_format((float) $month['revenue'], 0, ',', '.')) : '&middot;' ?></span>
                            <div class="chart__track">
                                <div class="chart__fill" style="height: <?= $height ?>%" title="<?= e($month['label'] . ': ' . money((float) $month['revenue'])) ?>"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="chart__labels">
                    <?php foreach ($revenue as $month): ?>
                        <span class="chart__label"><?= e($month['label']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel__head">
            <h2>Order pipeline</h2>
            <a class="panel__link" href="<?= url('/admin/orders') ?>">Manage <?= icon('arrow-right', 14) ?></a>
        </div>
        <div class="panel__body">
            <div class="mini-list" style="margin:-12px -22px">
                <?php foreach ($pipeline as $status => $row): ?>
                    <a class="mini-row" href="<?= e(url('/admin/orders?status=' . $status)) ?>">
                        <span class="badge <?= e($row['class']) ?>"><?= e($row['label']) ?></span>
                        <span class="mini-row__meta"><?= $row['count'] === 1 ? '1 order' : (int) $row['count'] . ' orders' ?></span>
                        <span class="mini-row__value"><?= icon('arrow-right', 15) ?></span>
                    </a>
                <?php endforeach; ?>
                <div class="mini-row">
                    <span class="badge badge--danger">Cancelled</span>
                    <span class="mini-row__meta"><?= (int) ($orderStats['cancelled'] ?? 0) ?> orders</span>
                    <span class="mini-row__value muted">&mdash;</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="admin-grid admin-grid--2">
    <div class="panel">
        <div class="panel__head">
            <h2>Latest orders</h2>
            <a class="panel__link" href="<?= url('/admin/orders') ?>">View all <?= icon('arrow-right', 14) ?></a>
        </div>
        <div class="panel__body panel__body--flush">
            <?php if ($recent === []): ?>
                <p class="muted small" style="padding:20px 22px;margin:0">No orders yet.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            <?php foreach ($recent as $order): ?>
                                <tr>
                                    <td>
                                        <div class="table__name"><?= e($order['order_number']) ?></div>
                                        <div class="table__sub"><?= e(date('d M, H:i', strtotime((string) $order['created_at']))) ?></div>
                                    </td>
                                    <td>
                                        <div class="table__name"><?= e($order['customer_name']) ?></div>
                                        <div class="table__sub"><?= e($order['shipping_city']) ?></div>
                                    </td>
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

    <div class="panel">
        <div class="panel__head">
            <h2>Low stock</h2>
            <a class="panel__link" href="<?= url('/admin/products?status=low_stock') ?>">Restock <?= icon('arrow-right', 14) ?></a>
        </div>
        <div class="panel__body panel__body--flush">
            <?php if ($lowStock === []): ?>
                <p class="muted small" style="padding:20px 22px;margin:0">Every product is comfortably in stock.</p>
            <?php else: ?>
                <div class="mini-list">
                    <?php foreach ($lowStock as $product): ?>
                        <a class="mini-row" href="<?= e(url('/admin/products/' . $product['id'] . '/edit')) ?>">
                            <span class="mini-row__thumb"><img src="<?= e(product_image($product['image'])) ?>" alt=""></span>
                            <span>
                                <span class="mini-row__name"><?= e($product['name']) ?></span>
                                <span class="mini-row__meta"><?= e($product['category_name'] ?? 'Uncategorised') ?></span>
                            </span>
                            <span class="mini-row__value <?= (int) $product['stock'] < 1 ? 'mini-row__value--out' : 'mini-row__value--low' ?>">
                                <?= (int) $product['stock'] < 1 ? 'Sold out' : (int) $product['stock'] . ' left' ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel__head">
        <h2>Best sellers</h2>
        <span class="small muted">By units sold, all time</span>
    </div>
    <div class="panel__body panel__body--flush">
        <?php if ($topProducts === []): ?>
            <p class="muted small" style="padding:20px 22px;margin:0">Sales will appear here once orders come in.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Product</th><th>Units sold</th><th>Revenue</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($topProducts as $product): ?>
                            <tr>
                                <td>
                                    <div class="table__product">
                                        <span class="table__thumb"><img src="<?= e(product_image($product['image'])) ?>" alt=""></span>
                                        <span>
                                            <span class="table__name"><?= e($product['name']) ?></span>
                                            <span class="table__sub"><?= e(money((float) $product['price'])) ?></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="num"><?= (int) $product['units_sold'] ?></td>
                                <td class="num"><strong><?= e(money((float) $product['revenue'])) ?></strong></td>
                                <td style="text-align:right">
                                    <a class="btn btn--ghost btn--sm" href="<?= e(url('/admin/products/' . $product['id'] . '/edit')) ?>">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
