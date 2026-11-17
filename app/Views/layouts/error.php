<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= e($code ?? 500) ?> &middot; <?= e($title ?? 'Error') ?> &middot; <?= e(config('app.name')) ?></title>
    <link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="error-body">
    <header class="error-head">
        <div class="container">
            <a class="brand" href="<?= url('/') ?>">
                <span class="brand__mark"><?= icon('mountain', 22) ?></span>
                <span class="brand__name">Ski Store<small>Alpine equipment</small></span>
            </a>
        </div>
    </header>

    <main class="error-page">
        <div class="container">
            <div class="error-card">
                <div class="error-card__code"><?= e($code ?? 500) ?></div>
                <span class="eyebrow">Something went off-piste</span>
                <h1><?= e($title ?? 'Unexpected error') ?></h1>
                <p><?= e($message ?? 'The store ran into a problem. Please try again in a moment.') ?></p>
                <div class="btn-row">
                    <a class="btn btn--primary" href="<?= url('/') ?>"><?= icon('arrow-left', 17) ?> Back to the store</a>
                    <a class="btn btn--outline" href="<?= url('/shop') ?>">Browse the gear</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="error-foot">
        <div class="container">&copy; <?= date('Y') ?> <?= e(config('app.name')) ?> &middot; Sarajevo, Bosnia and Herzegovina</div>
    </footer>
</body>
</html>
