<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Account') ?> &middot; <?= e(config('app.name')) ?></title>
    <link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
<div class="auth-shell">
    <aside class="auth-visual">
        <img src="<?= e(asset('img/hero.svg')) ?>" alt="">
        <div class="auth-visual__overlay"></div>

        <a class="auth-visual__brand brand" href="<?= url('/') ?>">
            <span class="brand__mark"><?= icon('mountain', 22) ?></span>
            <span class="brand__name">Ski Store<small>Alpine equipment</small></span>
        </a>

        <div class="auth-visual__content">
            <span class="eyebrow eyebrow--light">Est. 2009 &middot; Sarajevo</span>
            <h2>Built for the days you remember.</h2>
            <p>Save your wishlist, track every order and re-order your favourite gear in two clicks.</p>
            <div class="auth-visual__stats">
                <div><strong>18</strong><span>Gear pieces</span></div>
                <div><strong>200 KM</strong><span>Free delivery</span></div>
                <div><strong>30 days</strong><span>Easy returns</span></div>
            </div>
        </div>
    </aside>

    <div class="auth-panel">
        <div class="auth-card">
            <?php \App\Core\View::partial('partials/flash', ['plain' => true]); ?>
            <?= $content ?>
        </div>
        <p class="small muted text-center" style="margin-top:30px">
            &copy; <?= date('Y') ?> <?= e(config('app.name')) ?> &middot; <a href="<?= url('/') ?>">Back to the store</a>
        </p>
    </div>
</div>
<script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
