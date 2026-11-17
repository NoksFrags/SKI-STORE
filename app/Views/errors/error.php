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
