<?php

use App\Core\View;

$heroProduct = $featured[0] ?? ($newest[0] ?? null);
$totalGear = (int) ($productCount ?? 0);
?>
<section class="hero">
    <div class="hero__media">
        <img src="<?= e(asset('img/hero.svg')) ?>" alt="Snow covered mountain range at dusk" width="1600" height="900">
    </div>
    <div class="hero__overlay"></div>

    <div class="container hero__inner">
        <div class="hero__content">
            <span class="eyebrow eyebrow--light">Winter 2026 &middot; Sarajevo</span>
            <h1 class="hero__title">Skis, boards and boots <span>we ride ourselves.</span></h1>
            <p class="hero__text">
                Chosen by people who ski every week. Every pair arrives mounted, tuned and
                backed by a workshop that answers the phone in January.
            </p>
            <div class="hero__actions">
                <a class="btn btn--primary btn--lg" href="<?= url('/shop') ?>">
                    <?= icon('cart', 19) ?> Shop the collection
                </a>
                <a class="hero__secondary" href="<?= e(url('/shop?category=skis')) ?>">
                    Browse skis <?= icon('arrow-right', 15) ?>
                </a>
            </div>
            <div class="hero__stats">
                <div class="hero__stat">
                    <strong><?= $totalGear ?> pieces</strong>
                    <span>In stock now</span>
                </div>
                <div class="hero__stat">
                    <strong>200 KM</strong>
                    <span>Free delivery from</span>
                </div>
                <div class="hero__stat">
                    <strong>30 days</strong>
                    <span>Returns, no questions</span>
                </div>
            </div>
        </div>

        <?php if ($heroProduct !== null): ?>
            <aside class="hero__feature">
                <a class="hero__feature-media" href="<?= e(url('/product/' . $heroProduct['slug'])) ?>" tabindex="-1" aria-hidden="true">
                    <img src="<?= e(product_image($heroProduct['image'] ?? null)) ?>"
                         alt="" width="800" height="600" fetchpriority="high">
                </a>
                <div class="hero__feature-body">
                    <span class="hero__feature-brand"><?= e($heroProduct['brand'] ?: 'Ski Store') ?></span>
                    <h2 class="hero__feature-name"><?= e($heroProduct['name']) ?></h2>
                    <p class="hero__feature-meta"><?= e(excerpt($heroProduct['short_description'] ?? '', 70)) ?></p>
                    <div class="hero__feature-foot">
                        <span class="price-now hero__price"><?= e(money((float) $heroProduct['price'])) ?></span>
                        <a class="btn btn--light btn--sm" href="<?= e(url('/product/' . $heroProduct['slug'])) ?>">
                            View <?= icon('arrow-right', 15) ?>
                        </a>
                    </div>
                </div>
            </aside>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section__head">
            <div>
                <h2>Skis, boards, boots and the layers between</h2>
                <p>Six departments and <?= $totalGear ?> pieces in stock &mdash; each one ridden by someone on the team.</p>
            </div>
            <a class="section__link" href="<?= url('/shop') ?>">All categories <?= icon('arrow-right', 15) ?></a>
        </div>

        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <a class="category-card" href="<?= e(url('/shop?category=' . urlencode((string) $category['slug']))) ?>">
                    <span class="category-card__icon"><?= View::capture('partials/category-icon', ['icon' => $category['icon'], 'size' => 23]) ?></span>
                    <span class="category-card__name"><?= e($category['name']) ?></span>
                    <span class="category-card__count"><?= (int) $category['product_count'] ?> products</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($featured !== []): ?>
    <section class="section">
        <div class="container">
            <div class="section__head">
                <div>
                    <span class="eyebrow">Staff picks</span>
                    <h2>This month's crew favourites</h2>
                    <p><?= count($featured) ?> setups our team actually skis on their days off.</p>
                </div>
                <a class="section__link" href="<?= url('/shop') ?>">Shop all <?= icon('arrow-right', 15) ?></a>
            </div>

            <div class="product-grid">
                <?php foreach ($featured as $product): ?>
                    <?php View::partial('partials/product-card', ['product' => $product]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="section">
    <div class="container">
        <div class="promo">
            <div class="promo__content">
                <span class="eyebrow eyebrow--light">Workshop service</span>
                <h2>Buy the setup, not just the skis.</h2>
                <p>
                    Every ski and board bought here comes with a free factory tune, binding mount and
                    a 30-minute fit session with our bootfitters. Drop by the Sarajevo workshop or
                    send your gear in.
                </p>
                <div class="promo__actions btn-row">
                    <a class="btn btn--primary" href="<?= url('/shop?category=ski-boots') ?>">Boot fitting &amp; boots</a>
                    <a class="btn btn--quiet-light" href="<?= url('/shop?category=accessories') ?>">Tuning gear</a>
                </div>
            </div>
            <div class="promo__list">
                <div class="promo__item">
                    <?= icon('shield', 22) ?>
                    <div><strong>Free mount &amp; tune</strong>With every ski or board purchase</div>
                </div>
                <div class="promo__item">
                    <?= icon('snow', 22) ?>
                    <div><strong>Season-long support</strong>We answer gear questions all winter</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($newest !== []): ?>
    <section class="section">
        <div class="container">
            <div class="section__head">
                <div>
                    <span class="eyebrow">Just landed</span>
                    <h2>The newest pieces on the floor</h2>
                    <p><?= count($newest) ?> additions this month, freshest first.</p>
                </div>
                <a class="section__link" href="<?= e(url('/shop?sort=newest')) ?>">See what's new <?= icon('arrow-right', 15) ?></a>
            </div>

            <div class="product-grid product-grid--three">
                <?php foreach ($newest as $product): ?>
                    <?php View::partial('partials/product-card', ['product' => $product]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="section--tight">
    <div class="container">
        <div class="features">
            <div class="feature">
                <span class="feature__icon"><?= icon('truck', 20) ?></span>
                <div><strong>Free delivery over 200 KM</strong><span>Flat 9,90 KM below that</span></div>
            </div>
            <div class="feature">
                <span class="feature__icon"><?= icon('shield', 20) ?></span>
                <div><strong>30-day returns</strong><span>Unused gear, full refund</span></div>
            </div>
            <div class="feature">
                <span class="feature__icon"><?= icon('card', 20) ?></span>
                <div><strong>Secure checkout</strong><span>Card, cash or bank transfer</span></div>
            </div>
            <div class="feature">
                <span class="feature__icon"><?= icon('users', 20) ?></span>
                <div><strong>Real people, real advice</strong><span>Mon&ndash;Sat, 09:00&ndash;20:00</span></div>
            </div>
        </div>
    </div>
</section>
