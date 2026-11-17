<?php foreach (($items ?? []) as $line): ?>
    <div class="order-line">
        <div class="order-line__img">
            <img src="<?= e(product_image($line['image'] ?? null)) ?>" alt="<?= e($line['product_name']) ?>" loading="lazy">
        </div>
        <div class="grow">
            <div class="order-line__name">
                <?php if (! empty($line['slug'])): ?>
                    <a href="<?= e(url('/product/' . $line['slug'])) ?>"><?= e($line['product_name']) ?></a>
                <?php else: ?>
                    <?= e($line['product_name']) ?>
                <?php endif; ?>
            </div>
            <div class="order-line__meta"><?= (int) $line['quantity'] ?> &times; <?= e(money((float) $line['unit_price'])) ?></div>
        </div>
        <div class="order-line__price"><?= e(money((float) $line['line_total'])) ?></div>
    </div>
<?php endforeach; ?>
