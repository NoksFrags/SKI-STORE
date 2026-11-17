<?php

use App\Core\View;
use App\Models\Order;

$firstName = explode(' ', (string) $user['full_name'])[0];
?>
<section class="section section--tight">
    <div class="container">
        <div class="account-layout">
            <?php View::partial('partials/account-nav'); ?>

            <div class="account-main">
                <div class="account-intro">
                    <h1>Welcome back, <?= e($firstName) ?></h1>
                    <p>Your orders, saved gear and delivery details in one place.</p>
                </div>

                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-card__label">Orders</div>
                        <div class="stat-card__value"><?= (int) $orderCount ?></div>
                        <div class="stat-card__hint">All time</div>
                        <span class="stat-card__icon"><?= icon('package', 18) ?></span>
                    </div>
                    <div class="stat-card stat-card--success">
                        <div class="stat-card__label">Total spent</div>
                        <div class="stat-card__value" style="font-size:1.35rem"><?= e(money($totalSpent)) ?></div>
                        <div class="stat-card__hint">Excludes cancelled orders</div>
                        <span class="stat-card__icon"><?= icon('card', 18) ?></span>
                    </div>
                    <div class="stat-card stat-card--accent">
                        <div class="stat-card__label">Wishlist</div>
                        <div class="stat-card__value"><?= (int) $wishlistCount ?></div>
                        <div class="stat-card__hint">Saved items</div>
                        <span class="stat-card__icon"><?= icon('heart', 18) ?></span>
                    </div>
                    <div class="stat-card stat-card--info">
                        <div class="stat-card__label">In your cart</div>
                        <div class="stat-card__value"><?= (int) $totals['count'] ?></div>
                        <div class="stat-card__hint"><?= e(money($totals['total'])) ?> total</div>
                        <span class="stat-card__icon"><?= icon('cart', 18) ?></span>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel__head">
                        <h2>Recent orders</h2>
                        <a class="panel__link" href="<?= url('/account/orders') ?>">All orders <?= icon('arrow-right', 14) ?></a>
                    </div>

                    <?php if ($orders === []): ?>
                        <div class="panel__body">
                            <div class="empty" style="border-style:solid;padding:34px 20px">
                                <span class="empty__icon"><?= icon('package', 24) ?></span>
                                <div class="empty__title">No orders yet</div>
                                <p class="empty__text">When you place an order it will show up here with live status.</p>
                                <a class="btn btn--primary btn--sm" href="<?= url('/shop') ?>">Start shopping</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr><th>Order</th><th>Placed</th><th>Status</th><th>Total</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><strong><?= e($order['order_number']) ?></strong></td>
                                            <td class="muted"><?= e(date('d M Y', strtotime((string) $order['created_at']))) ?></td>
                                            <td><span class="badge <?= e(order_status_class((string) $order['status'])) ?>"><?= e(Order::statusLabel((string) $order['status'])) ?></span></td>
                                            <td class="num"><strong><?= e(money((float) $order['total'])) ?></strong></td>
                                            <td style="text-align:right">
                                                <a class="btn btn--outline btn--sm" href="<?= url('/account/orders/' . $order['id']) ?>">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="admin-grid admin-grid--even">
                    <div class="panel">
                        <div class="panel__head">
                            <h2>Saved for later</h2>
                            <a class="panel__link" href="<?= url('/wishlist') ?>">Wishlist <?= icon('arrow-right', 14) ?></a>
                        </div>
                        <div class="panel__body">
                            <?php if ($wishlistItems === []): ?>
                                <p class="muted small" style="margin:0">No saved items yet. Tap the heart on a product to keep it here.</p>
                            <?php else: ?>
                                <?php foreach ($wishlistItems as $saved): ?>
                                    <div class="summary-item">
                                        <div class="summary-item__thumb">
                                            <img src="<?= e(product_image($saved['image'])) ?>" alt="<?= e($saved['name']) ?>">
                                        </div>
                                        <div>
                                            <div class="summary-item__name">
                                                <a href="<?= e(url('/product/' . $saved['slug'])) ?>"><?= e($saved['name']) ?></a>
                                            </div>
                                            <div class="summary-item__meta"><?= e($saved['category_name'] ?? 'Gear') ?></div>
                                        </div>
                                        <div class="summary-item__price"><?= e(money((float) $saved['price'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel__head">
                            <h2>Profile</h2>
                            <a class="panel__link" href="<?= url('/account/profile') ?>">Edit <?= icon('arrow-right', 14) ?></a>
                        </div>
                        <div class="panel__body">
                            <div class="profile-list" style="grid-template-columns:1fr">
                                <div class="profile-list__item"><span>Name</span><strong><?= e($user['full_name']) ?></strong></div>
                                <div class="profile-list__item"><span>Email</span><strong><?= e($user['email']) ?></strong></div>
                                <div class="profile-list__item"><span>Phone</span><strong><?= e($user['phone'] ?: 'Not set') ?></strong></div>
                                <div class="profile-list__item"><span>Delivery city</span><strong><?= e($user['city'] ?: 'Not set') ?></strong></div>
                                <div class="profile-list__item">
                                    <span>Password</span>
                                    <strong><a class="section__link" href="<?= url('/account/password') ?>">Change password</a></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
