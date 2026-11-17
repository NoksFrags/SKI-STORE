<?php

use App\Core\Auth;

$currentUser = Auth::user();
$path = request_path();
$firstName = $currentUser !== null ? explode(' ', (string) $currentUser['full_name'])[0] : '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Alpine gear') ?> &middot; <?= e(config('app.name')) ?></title>
    <meta name="description" content="Skis, snowboards, boots and alpine outerwear for every mountain day. Free delivery on orders over 200 KM.">
    <link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
<a class="sr-only" href="#main">Skip to content</a>

<div class="topbar">
    <div class="container topbar__inner">
        <span class="topbar__note">
            <?= icon('truck', 16) ?>
            Free delivery on orders over <?= e(money((float) config('app.free_shipping_from'))) ?>
        </span>
        <div class="topbar__links">
            <a href="tel:+38733555100"><?= icon('phone', 15) ?> +387 33 555 100</a>
            <span class="muted">Mon&ndash;Sat &middot; 09:00&ndash;20:00</span>
        </div>
    </div>
</div>

<header class="site-header" data-header>
    <div class="container site-header__inner">
        <a class="brand" href="<?= url('/') ?>">
            <span class="brand__mark"><?= icon('mountain', 22) ?></span>
            <span class="brand__name">Ski Store<small>Alpine equipment</small></span>
        </a>

        <nav class="main-nav" id="main-nav" aria-label="Main navigation">
            <button class="nav-close" type="button" data-nav-close aria-label="Close menu"><?= icon('close', 20) ?></button>
            <a class="main-nav__link <?= $path === '/' ? 'is-active' : '' ?>" data-nav-link href="<?= url('/') ?>">Home</a>
            <a class="main-nav__link <?= $path === '/shop' ? 'is-active' : '' ?>" data-nav-link href="<?= url('/shop') ?>">Shop</a>

            <div class="nav-dropdown" data-dropdown>
                <button class="main-nav__link" type="button" data-dropdown-toggle aria-expanded="false">
                    Categories <?= icon('chevron', 14) ?>
                </button>
                <div class="nav-dropdown__panel">
                    <?php foreach (nav_categories() as $category): ?>
                        <a class="nav-dropdown__link" href="<?= e(url('/shop?category=' . urlencode((string) $category['slug']))) ?>">
                            <span class="nav-dropdown__icon"><?= \App\Core\View::capture('partials/category-icon', ['icon' => $category['icon'], 'size' => 18]) ?></span>
                            <?= e($category['name']) ?>
                            <span class="nav-dropdown__count"><?= (int) $category['product_count'] ?></span>
                        </a>
                    <?php endforeach; ?>
                    <a class="nav-dropdown__link nav-dropdown__all" href="<?= url('/shop') ?>">
                        Browse everything <?= icon('arrow-right', 15) ?>
                    </a>
                </div>
            </div>

            <a class="main-nav__link <?= ($_GET['sort'] ?? '') === 'newest' && $path === '/shop' ? 'is-active' : '' ?>" data-nav-link href="<?= e(url('/shop?sort=newest')) ?>">New arrivals</a>

            <form class="header-search header-search--mobile" action="<?= url('/shop') ?>" method="get" role="search">
                <span class="header-search__icon"><?= icon('search', 18) ?></span>
                <input class="input" type="search" name="q" placeholder="Search skis, boards, boots&hellip;" value="<?= e($_GET['q'] ?? '') ?>" aria-label="Search products">
            </form>
        </nav>

        <form class="header-search" action="<?= url('/shop') ?>" method="get" role="search">
            <span class="header-search__icon"><?= icon('search', 18) ?></span>
            <input class="input" type="search" name="q" placeholder="Search gear&hellip;" value="<?= e($_GET['q'] ?? '') ?>" aria-label="Search products">
        </form>

        <div class="header-actions">
            <a class="icon-btn" href="<?= url('/wishlist') ?>" aria-label="Wishlist">
                <?= icon('heart', 21) ?>
                <?php if (wishlist_count() > 0): ?>
                    <span class="icon-btn__badge"><?= (int) wishlist_count() ?></span>
                <?php endif; ?>
            </a>
            <a class="icon-btn" href="<?= url('/cart') ?>" aria-label="Shopping cart">
                <?= icon('cart', 21) ?>
                <?php if (cart_quantity() > 0): ?>
                    <span class="icon-btn__badge"><?= (int) cart_quantity() ?></span>
                <?php endif; ?>
            </a>

            <?php if ($currentUser !== null): ?>
                <div class="account-menu" data-dropdown>
                    <button class="account-menu__trigger" type="button" data-dropdown-toggle aria-expanded="false">
                        <span class="account-menu__avatar"><?= e(user_initials((string) $currentUser['full_name'])) ?></span>
                        <span class="account-menu__label"><?= e($firstName) ?></span>
                        <?= icon('chevron', 14) ?>
                    </button>
                    <div class="account-menu__panel">
                        <div class="account-menu__head">
                            <div class="account-menu__name"><?= e($currentUser['full_name']) ?></div>
                            <div class="account-menu__mail"><?= e($currentUser['email']) ?></div>
                        </div>
                        <a class="account-menu__link" href="<?= url('/account') ?>"><?= icon('grid', 17) ?> Dashboard</a>
                        <a class="account-menu__link" href="<?= url('/account/orders') ?>"><?= icon('package', 17) ?> My orders</a>
                        <a class="account-menu__link" href="<?= url('/wishlist') ?>"><?= icon('heart', 17) ?> Wishlist</a>
                        <?php if ($currentUser['role'] === 'admin'): ?>
                            <a class="account-menu__link" href="<?= url('/admin') ?>"><?= icon('shield', 17) ?> Admin panel</a>
                        <?php endif; ?>
                        <div class="account-menu__sep"></div>
                        <form method="post" action="<?= url('/logout') ?>">
                            <?= csrf_field() ?>
                            <button class="account-menu__link account-menu__link--danger" type="submit"><?= icon('logout', 17) ?> Sign out</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a class="btn btn--dark btn--sm account-signin" href="<?= url('/login') ?>"><?= icon('user', 16) ?> Sign in</a>
            <?php endif; ?>

            <button class="nav-toggle icon-btn" type="button" data-nav-toggle aria-expanded="false" aria-controls="main-nav" aria-label="Open menu">
                <?= icon('menu', 22) ?>
            </button>
        </div>
    </div>
</header>

<?php \App\Core\View::partial('partials/flash'); ?>

<main id="main">
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer__grid">
            <div class="footer__brand">
                <a class="brand" href="<?= url('/') ?>">
                    <span class="brand__mark"><?= icon('mountain', 22) ?></span>
                    <span class="brand__name">Ski Store<small>Alpine equipment</small></span>
                </a>
                <p>Independent mountain shop since 2009. We test every ski, board and shell we sell on the hills around Sarajevo.</p>
                <div class="footer__pay">
                    <span>Visa</span><span>Mastercard</span><span>Maestro</span><span>Cash on delivery</span>
                </div>
            </div>

            <div>
                <div class="footer__title">Shop</div>
                <div class="footer__links">
                    <a href="<?= url('/shop') ?>">All gear</a>
                    <?php foreach (array_slice(nav_categories(), 0, 4) as $category): ?>
                        <a href="<?= e(url('/shop?category=' . urlencode((string) $category['slug']))) ?>"><?= e($category['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <div class="footer__title">Your account</div>
                <div class="footer__links">
                    <a href="<?= url('/account') ?>">Dashboard</a>
                    <a href="<?= url('/account/orders') ?>">Order history</a>
                    <a href="<?= url('/wishlist') ?>">Wishlist</a>
                    <a href="<?= url('/cart') ?>">Shopping cart</a>
                    <a href="<?= url('/account/profile') ?>">Profile &amp; address</a>
                </div>
            </div>

            <div>
                <div class="footer__title">Visit the shop</div>
                <div class="footer__contact">
                    <div><?= icon('pin', 17) ?><span>Zmaja od Bosne 4<br>71000 Sarajevo, Bosnia and Herzegovina</span></div>
                    <div><?= icon('phone', 17) ?><a href="tel:+38733555100">+387 33 555 100</a></div>
                    <div><?= icon('mail', 17) ?><a href="mailto:hello@skistore.test">hello@skistore.test</a></div>
                    <div><?= icon('snow', 17) ?><span>Mon&ndash;Sat 09:00&ndash;20:00</span></div>
                </div>
            </div>
        </div>

        <div class="footer__bottom">
            <span>&copy; <?= date('Y') ?> <?= e(config('app.name')) ?>. All rights reserved.</span>
            <span>Prices in <?= e(config('app.currency')) ?> &middot; Free delivery over <?= e(money((float) config('app.free_shipping_from'))) ?></span>
        </div>
    </div>
</footer>

<script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
