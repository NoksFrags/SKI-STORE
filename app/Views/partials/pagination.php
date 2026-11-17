<?php
$current = max(1, (int) ($page ?? 1));
$pages = max(1, (int) ($pages ?? 1));

if ($pages <= 1) {
    return;
}

$start = max(1, min($current - 2, $pages - 4));
$end = min($pages, $start + 4);
?>
<nav class="pagination" aria-label="Pagination">
    <a class="pagination__link <?= $current <= 1 ? 'is-disabled' : '' ?>" href="<?= e(url(query_url(['page' => max(1, $current - 1)]))) ?>" aria-label="Previous page">
        <?= icon('arrow-left', 16) ?>
    </a>

    <?php if ($start > 1): ?>
        <a class="pagination__link" href="<?= e(url(query_url(['page' => 1]))) ?>">1</a>
        <?php if ($start > 2): ?><span class="pagination__link is-disabled">&hellip;</span><?php endif; ?>
    <?php endif; ?>

    <?php for ($index = $start; $index <= $end; $index++): ?>
        <a class="pagination__link <?= $index === $current ? 'is-active' : '' ?>"
           href="<?= e(url(query_url(['page' => $index]))) ?>"
           <?= $index === $current ? 'aria-current="page"' : '' ?>><?= $index ?></a>
    <?php endfor; ?>

    <?php if ($end < $pages): ?>
        <?php if ($end < $pages - 1): ?><span class="pagination__link is-disabled">&hellip;</span><?php endif; ?>
        <a class="pagination__link" href="<?= e(url(query_url(['page' => $pages]))) ?>"><?= $pages ?></a>
    <?php endif; ?>

    <a class="pagination__link <?= $current >= $pages ? 'is-disabled' : '' ?>" href="<?= e(url(query_url(['page' => min($pages, $current + 1)]))) ?>" aria-label="Next page">
        <?= icon('arrow-right', 16) ?>
    </a>
</nav>
