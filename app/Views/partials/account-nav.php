<?php

use App\Core\Auth;

$accountUser = Auth::user();
$accountPath = request_path();
?>
<aside class="account-nav">
    <div class="account-nav__user">
        <span class="account-nav__avatar"><?= e(user_initials((string) ($accountUser['full_name'] ?? 'Customer'))) ?></span>
        <div>
            <div class="account-nav__name"><?= e($accountUser['full_name'] ?? 'Customer') ?></div>
            <div class="account-nav__role"><?= ($accountUser['role'] ?? 'customer') === 'admin' ? 'Administrator' : 'Customer' ?></div>
        </div>
    </div>

    <a class="account-nav__link <?= $accountPath === '/account' ? 'is-active' : '' ?>" href="<?= url('/account') ?>">
        <?= icon('grid', 18) ?> Dashboard
    </a>
    <a class="account-nav__link <?= is_active('/account/orders') ? 'is-active' : '' ?>" href="<?= url('/account/orders') ?>">
        <?= icon('package', 18) ?> My orders
    </a>
    <a class="account-nav__link <?= $accountPath === '/wishlist' ? 'is-active' : '' ?>" href="<?= url('/wishlist') ?>">
        <?= icon('heart', 18) ?> Wishlist
    </a>
    <a class="account-nav__link <?= $accountPath === '/account/profile' ? 'is-active' : '' ?>" href="<?= url('/account/profile') ?>">
        <?= icon('user', 18) ?> Profile details
    </a>
    <a class="account-nav__link <?= $accountPath === '/account/password' ? 'is-active' : '' ?>" href="<?= url('/account/password') ?>">
        <?= icon('lock', 18) ?> Change password
    </a>

    <?php if (($accountUser['role'] ?? 'customer') === 'admin'): ?>
        <a class="account-nav__link" href="<?= url('/admin') ?>">
            <?= icon('shield', 18) ?> Admin panel
        </a>
    <?php endif; ?>

    <div class="account-nav__sep"></div>

    <form method="post" action="<?= url('/logout') ?>">
        <?= csrf_field() ?>
        <button class="account-nav__link account-nav__link--danger" type="submit">
            <?= icon('logout', 18) ?> Sign out
        </button>
    </form>
</aside>
