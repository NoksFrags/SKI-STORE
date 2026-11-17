<?php
$icon = $icon ?? 'snow';
$size = icon_size($size ?? 20);

$paths = [
    'ski'       => '<path d="M4 19 17 4"/><path d="M9 20 20 6"/><path d="M15 3.5 18 2l1.5 3"/><path d="M6 21l3-1"/>',
    'snowboard' => '<rect x="8" y="2.5" width="8" height="19" rx="4" transform="rotate(18 12 12)"/><path d="M9.5 8.5h5M9.5 15.5h5"/>',
    'boot'      => '<path d="M6 3.5h7v7l5 2.5v5.5H6z"/><path d="M6 18.5h12"/><path d="M9 7h4M9 10h4"/>',
    'jacket'    => '<path d="m9 3 3 2 3-2 4 4-2 3.5-1.5-1.2V21H7.5V9.3L6 10.5 4 7z"/><path d="M12 5v16"/>',
    'helmet'    => '<path d="M3.5 14a8.5 8.5 0 0 1 17 0v2.5h-17z"/><path d="M3.5 16.5h17"/><path d="M8 19.5c1 2 7 2 8 0"/>',
    'gear'      => '<circle cx="12" cy="12" r="3"/><path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M18.7 5.3l-2.1 2.1M7.4 16.6l-2.1 2.1"/>',
    'snow'      => '<path d="M12 2v20M3.5 7l17 10M20.5 7l-17 10"/>',
];
?>
<svg class="icon" width="<?= $size ?>" height="<?= $size ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <?= $paths[$icon] ?? $paths['snow'] ?>
</svg>
