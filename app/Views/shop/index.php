<?php

use App\Core\View;

$sortOptions = [
    'newest'     => 'Newest first',
    'price_asc'  => 'Price: low to high',
    'price_desc' => 'Price: high to low',
    'name_asc'   => 'Name: A to Z',
];
$activeSort = $filters['sort'] ?? 'newest';
?>
<section class="page-head page-head--compact">
    <div class="container page-head__inner">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= url('/') ?>">Home</a>
            <?= icon('chevron', 13) ?>
            <a href="<?= url('/shop') ?>">Shop</a>
            <?php if ($activeCategory !== null): ?>
                <?= icon('chevron', 13) ?>
                <span><?= e($activeCategory['name']) ?></span>
            <?php endif; ?>
        </nav>
        <h1><?= e($activeCategory['name'] ?? 'All the gear') ?></h1>
        <p>
            <?php if ($activeCategory !== null): ?>
                <?= e(excerpt($activeCategory['description'] ?? '', 150)) ?>
            <?php else: ?>
                Skis, snowboards, boots and layers - hand-picked for the mountains around us.
            <?php endif; ?>
        </p>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <div class="shop-layout">
            <aside class="filters">
                <div class="filters__group">
                    <p class="filters__title">Categories</p>
                    <div class="filter-list">
                        <a class="filter-link <?= ($filters['category'] ?? '') === '' ? 'is-active' : '' ?>" href="<?= e(url(query_url(['category' => null, 'page' => null]))) ?>">
                            All gear <span class="filter-link__count"><?= (int) $total ?></span>
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <a class="filter-link <?= ($filters['category'] ?? '') === $category['slug'] ? 'is-active' : '' ?>"
                               href="<?= e(url(query_url(['category' => $category['slug'], 'page' => null]))) ?>">
                                <?= e($category['name']) ?>
                                <span class="filter-link__count"><?= (int) $category['product_count'] ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filters__group">
                    <p class="filters__title">Need a hand?</p>
                    <p class="small muted" style="margin-bottom:10px">Not sure which length or flex? Call the shop and talk to a bootfitter.</p>
                    <a class="section__link" href="tel:+38733555100"><?= icon('phone', 15) ?> +387 33 555 100</a>
                </div>
            </aside>

            <div>
                <div class="shop-toolbar">
                    <div class="shop-toolbar__result">
                        <strong><?= (int) $total ?></strong> <?= (int) $total === 1 ? 'item' : 'items' ?>
                        <?php if ($activeCategory !== null): ?>in <?= e($activeCategory['name']) ?><?php endif; ?>
                        <?php if (! empty($filters['search'])): ?>for &ldquo;<?= e($filters['search']) ?>&rdquo;<?php endif; ?>
                    </div>

                    <form class="shop-toolbar__controls" method="get" action="<?= url('/shop') ?>">
                        <?php if (! empty($filters['category'])): ?>
                            <input type="hidden" name="category" value="<?= e($filters['category']) ?>">
                        <?php endif; ?>
                        <input type="hidden" name="sort" value="<?= e($activeSort) ?>">
                        <div class="shop-search">
                            <?= icon('search', 17) ?>
                            <input class="input" type="search" name="q" value="<?= e($filters['search'] ?? '') ?>" placeholder="Search this collection" aria-label="Search products">
                        </div>

                        <div class="sort-menu" data-sort-menu>
                            <button class="sort-menu__trigger" type="button" data-sort-toggle
                                    aria-expanded="false" aria-haspopup="true">
                                <?= icon('sliders', 16) ?>
                                <span class="sort-menu__label"><?= e($sortOptions[$activeSort] ?? 'Newest first') ?></span>
                                <?= icon('chevron', 14) ?>
                            </button>
                            <div class="sort-menu__panel" role="menu" aria-label="Sort products">
                                <p class="sort-menu__title">Sort by</p>
                                <?php foreach ($sortOptions as $value => $label): ?>
                                    <a class="sort-menu__link <?= $activeSort === $value ? 'is-active' : '' ?>"
                                       role="menuitem"
                                       <?= $activeSort === $value ? 'aria-current="true"' : '' ?>
                                       href="<?= e(url(query_url(['sort' => $value, 'page' => null]))) ?>">
                                        <span class="sort-menu__tick"><?= $activeSort === $value ? icon('check', 15) : '' ?></span>
                                        <span><?= e($label) ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button class="btn btn--outline btn--sm" type="submit">Apply</button>
                    </form>
                </div>

                <?php if ($products === []): ?>
                    <div class="empty">
                        <span class="empty__icon"><?= icon('search', 26) ?></span>
                        <div class="empty__title">No gear matches those filters</div>
                        <p class="empty__text">Try a different search term or clear the filters to see the whole collection.</p>
                        <a class="btn btn--dark" href="<?= url('/shop') ?>">Clear filters</a>
                    </div>
                <?php else: ?>
                    <div class="product-grid">
                        <?php foreach ($products as $product): ?>
                            <?php View::partial('partials/product-card', ['product' => $product]); ?>
                        <?php endforeach; ?>
                    </div>

                    <?php View::partial('partials/pagination', ['page' => $page, 'pages' => $pages]); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
