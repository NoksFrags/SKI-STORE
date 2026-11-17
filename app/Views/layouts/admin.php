<?php

use App\Core\Auth;

$admin = Auth::user();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title ?? 'Admin') ?> &middot; <?= e(config('app.name')) ?> Admin</title>
    <link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a class="admin-sidebar__brand brand" href="<?= url('/admin') ?>">
            <span class="brand__mark"><?= icon('mountain', 22) ?></span>
            <span class="brand__name">Ski Store<small>Admin</small></span>
        </a>

        <nav class="admin-nav" aria-label="Admin navigation">
            <div class="admin-nav__label">Store</div>
            <a class="admin-nav__link <?= request_path() === '/admin' ? 'is-active' : '' ?>" href="<?= url('/admin') ?>">
                <?= icon('grid', 18) ?> Overview
            </a>
            <a class="admin-nav__link <?= is_active('/admin/products') ? 'is-active' : '' ?>" href="<?= url('/admin/products') ?>">
                <?= icon('package', 18) ?> Products
            </a>
            <a class="admin-nav__link <?= is_active('/admin/categories') ? 'is-active' : '' ?>" href="<?= url('/admin/categories') ?>">
                <?= icon('tag', 18) ?> Categories
            </a>
            <a class="admin-nav__link <?= is_active('/admin/orders') ? 'is-active' : '' ?>" href="<?= url('/admin/orders') ?>">
                <?= icon('receipt', 18) ?> Orders
            </a>

            <div class="admin-nav__label">People</div>
            <a class="admin-nav__link <?= is_active('/admin/users') ? 'is-active' : '' ?>" href="<?= url('/admin/users') ?>">
                <?= icon('users', 18) ?> Customers &amp; staff
            </a>

            <div class="admin-nav__label">Shortcuts</div>
            <a class="admin-nav__link" href="<?= url('/') ?>" target="_blank" rel="noopener">
                <?= icon('mountain', 18) ?> View storefront
            </a>
            <a class="admin-nav__link" href="<?= url('/admin/products/create') ?>">
                <?= icon('plus', 18) ?> New product
            </a>
        </nav>

        <div class="admin-sidebar__foot">
            <div class="admin-user">
                <span class="admin-user__avatar"><?= e(user_initials((string) ($admin['full_name'] ?? 'Admin'))) ?></span>
                <div>
                    <div class="admin-user__name"><?= e($admin['full_name'] ?? 'Administrator') ?></div>
                    <div class="admin-user__role">Store administrator</div>
                </div>
            </div>
            <form method="post" action="<?= url('/logout') ?>">
                <?= csrf_field() ?>
                <button class="admin-signout" type="submit"><?= icon('logout', 17) ?> Sign out</button>
            </form>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="admin-nav-toggle" type="button" data-admin-toggle aria-label="Toggle menu"><?= icon('menu', 20) ?></button>
            <div class="admin-topbar__title">
                <?= e($title ?? 'Overview') ?>
                <span>Ski Store control panel</span>
            </div>
            <div class="admin-topbar__actions">
                <form class="admin-search" action="<?= url('/admin/products') ?>" method="get" role="search">
                    <span class="icon"><?= icon('search', 17) ?></span>
                    <input class="input" type="search" name="q" placeholder="Search products&hellip;" value="<?= e($_GET['q'] ?? '') ?>" aria-label="Search products">
                </form>
                <a class="btn btn--outline btn--sm" href="<?= url('/') ?>" target="_blank" rel="noopener">
                    <?= icon('arrow-right', 15) ?> Store
                </a>
            </div>
        </header>

        <div class="admin-content">
            <?php \App\Core\View::partial('partials/flash'); ?>
            <?= $content ?>
        </div>
    </div>
</div>
<script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
