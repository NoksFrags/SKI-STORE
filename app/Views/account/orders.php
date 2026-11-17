<?php

use App\Core\View;
use App\Models\Order;
?>
<section class="section section--tight">
    <div class="container">
        <div class="account-layout">
            <?php View::partial('partials/account-nav'); ?>

            <div class="account-main">
                <div class="account-intro">
                    <h1>My orders</h1>
                    <p><?= (int) $total ?> order<?= (int) $total === 1 ? '' : 's' ?> placed with Ski Store.</p>
                </div>

                <?php if ($orders === []): ?>
                    <div class="empty">
                        <span class="empty__icon"><?= icon('package', 28) ?></span>
                        <div class="empty__title">No orders yet</div>
                        <p class="empty__text">Your first setup is waiting. Browse the collection and we will keep every order here.</p>
                        <a class="btn btn--primary" href="<?= url('/shop') ?>">Browse gear</a>
                    </div>
                <?php else: ?>
                    <div class="stack">
                        <?php foreach ($orders as $order): ?>
                            <article class="order-card">
                                <div class="order-card__head">
                                    <div>
                                        <div class="order-card__id"><?= e($order['order_number']) ?></div>
                                        <div class="order-card__meta">
                                            Placed <?= e(date('d M Y, H:i', strtotime((string) $order['created_at']))) ?>
                                            &middot; <?= e(Order::PAYMENT_METHODS[$order['payment_method']] ?? $order['payment_method']) ?>
                                        </div>
                                    </div>
                                    <span class="badge <?= e(order_status_class((string) $order['status'])) ?>"><?= e(Order::statusLabel((string) $order['status'])) ?></span>
                                </div>
                                <div class="order-card__body">
                                    <div class="small muted">
                                        Ship to <strong style="color:var(--ink)"><?= e($order['shipping_city']) ?></strong>,
                                        <?= e($order['shipping_country']) ?>
                                    </div>
                                    <a class="btn btn--outline btn--sm" href="<?= url('/account/orders/' . $order['id']) ?>">
                                        Order details <?= icon('arrow-right', 15) ?>
                                    </a>
                                </div>
                                <div class="order-card__foot">
                                    <span class="small muted">Order total</span>
                                    <span class="order-card__total"><?= e(money((float) $order['total'])) ?></span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <?php View::partial('partials/pagination', ['page' => $page, 'pages' => $pages]); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
