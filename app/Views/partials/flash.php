<?php

use App\Core\Flash;

$messages = Flash::pull();
$plain = (bool) ($plain ?? false);
?>
<?php if ($messages !== []): ?>
    <div class="flash-stack<?= $plain ? ' flash-stack--plain' : '' ?>">
        <?php foreach ($messages as $message): ?>
            <?php
            $type = (string) ($message['type'] ?? 'info');
            $type = in_array($type, ['success', 'error', 'warning', 'info'], true) ? $type : 'info';
            $iconName = match ($type) {
                'success' => 'check',
                'error'   => 'alert',
                'warning' => 'info',
                default   => 'info',
            };
            ?>
            <div class="alert alert--<?= e($type) ?>" role="status">
                <span class="alert__icon"><?= icon($iconName, 19) ?></span>
                <div class="alert__body"><?= e($message['message'] ?? '') ?></div>
                <button class="alert__close" type="button" data-dismiss aria-label="Dismiss message"><?= icon('close', 16) ?></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
