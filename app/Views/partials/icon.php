<?php

$name = $name ?? 'snow';
$size = icon_size($size ?? 20);

$icons = [
    'search'      => '<circle cx="11" cy="11" r="7"/><path d="m20.5 20.5-4.3-4.3"/>',
    'cart'        => '<path d="M6 8h12l-1.2 11.2a2 2 0 0 1-2 1.8H9.2a2 2 0 0 1-2-1.8L6 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
    'heart'       => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21.2l7.8-7.7 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/>',
    'user'        => '<circle cx="12" cy="8" r="4"/><path d="M4.5 21a7.5 7.5 0 0 1 15 0"/>',
    'menu'        => '<path d="M4 7h16M4 12h16M4 17h16"/>',
    'close'       => '<path d="m6 6 12 12M18 6 6 18"/>',
    'chevron'     => '<path d="m6 9 6 6 6-6"/>',
    'arrow-right' => '<path d="M4 12h16M14 6l6 6-6 6"/>',
    'arrow-left'  => '<path d="M20 12H4M10 6l-6 6 6 6"/>',
    'trash'       => '<path d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M6 7l1 13a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-13"/>',
    'plus'        => '<path d="M12 5v14M5 12h14"/>',
    'minus'       => '<path d="M5 12h14"/>',
    'check'       => '<path d="m5 13 4 4L19 7"/>',
    'package'     => '<path d="m12 3 9 5v8l-9 5-9-5V8l9-5Z"/><path d="m3 8 9 5 9-5M12 13v9"/>',
    'truck'       => '<path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18.5" r="1.6"/><circle cx="17.5" cy="18.5" r="1.6"/>',
    'shield'      => '<path d="M12 3 5 6v5c0 4.6 3 8.1 7 10 4-1.9 7-5.4 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/>',
    'snow'        => '<path d="M12 2v20M4.5 6.5l15 11M19.5 6.5l-15 11M12 6l-3-3M12 6l3-3"/>',
    'mountain'    => '<path d="m3 20 6-11 4 7 3-4 5 8Z"/><circle cx="17" cy="5" r="2"/>',
    'grid'        => '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
    'tag'         => '<path d="M20.5 12.5 12 21l-9-9V4h8l9.5 8.5Z"/><circle cx="7.5" cy="8.5" r="1.3"/>',
    'receipt'     => '<path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"/><path d="M9 8h6M9 12h6M9 16h4"/>',
    'users'       => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 5.2a3.5 3.5 0 0 1 0 5.6M17 14.6A6 6 0 0 1 21.5 20"/>',
    'logout'      => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/>',
    'star'        => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"/>',
    'lock'        => '<rect x="4" y="10" width="16" height="11" rx="3"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
    'mail'        => '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="m4 7 8 6 8-6"/>',
    'phone'       => '<path d="M6 3h3l2 5-2.5 1.5a11 11 0 0 0 5 5L15 12l5 2v3a2 2 0 0 1-2 2A15 15 0 0 1 4 5a2 2 0 0 1 2-2Z"/>',
    'pin'         => '<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
    'card'        => '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 10h18"/>',
    'edit'        => '<path d="M4 20h4l10-10-4-4L4 16v4Z"/><path d="m13.5 6.5 4 4"/>',
    'eye'         => '<path d="M2 12s3.6-6.5 10-6.5S22 12 22 12s-3.6 6.5-10 6.5S2 12 2 12Z"/><circle cx="12" cy="12" r="2.6"/>',
    'info'        => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/>',
    'alert'       => '<path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 9v5M12 17h.01"/>',
    'filter'      => '<path d="M3 6h18M6 12h12M10 18h4"/>',
    'sliders'     => '<path d="M4 8h16M4 16h16"/><circle cx="9" cy="8" r="2.4" fill="var(--icon-fill, #fff)"/><circle cx="15" cy="16" r="2.4" fill="var(--icon-fill, #fff)"/>',
    'sale'        => '<path d="M12 2v20M17 6.5c0-1.9-2.2-3-5-3s-5 1.1-5 3 2.2 3 5 3 5 1.1 5 3-2.2 3-5 3-5-1.1-5-3"/>',
    'gift'        => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M5 12v9h14v-9M12 8v13"/><path d="M12 8c-1-3-5-4-5-1.5S10 8 12 8Zm0 0c1-3 5-4 5-1.5S14 8 12 8Z"/>',
];
?>
<svg class="icon" width="<?= $size ?>" height="<?= $size ?>" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <?= $icons[$name] ?? $icons['snow'] ?>
</svg>
