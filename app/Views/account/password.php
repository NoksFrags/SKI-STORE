<?php

use App\Core\View;

$errors = errors();
?>
<section class="section section--tight">
    <div class="container">
        <div class="account-layout">
            <?php View::partial('partials/account-nav'); ?>

            <div class="account-main">
                <div class="account-intro">
                    <h1>Change password</h1>
                    <p>Use at least 8 characters and avoid reusing old passwords.</p>
                </div>

                <div class="panel">
                    <div class="panel__head">
                        <h2>Password</h2>
                        <span class="badge badge--muted"><?= icon('lock', 14) ?> Security</span>
                    </div>
                    <div class="panel__body">
                        <form method="post" action="<?= url('/account/password') ?>">
                            <?= csrf_field() ?>

                            <div class="form-grid">
                                <div class="field field--full">
                                    <label class="field__label" for="current_password">Current password <span class="req">*</span></label>
                                    <div class="input-group">
                                        <input class="input <?= isset($errors['current_password']) ? 'input--error' : '' ?>" type="password"
                                               id="current_password" name="current_password" autocomplete="current-password" required>
                                        <button class="pw-toggle" type="button" data-password-toggle="current_password">Show</button>
                                    </div>
                                    <?php if (isset($errors['current_password'])): ?><span class="field__error"><?= e($errors['current_password']) ?></span><?php endif; ?>
                                </div>

                                <div class="field">
                                    <label class="field__label" for="password">New password <span class="req">*</span></label>
                                    <div class="input-group">
                                        <input class="input <?= isset($errors['password']) ? 'input--error' : '' ?>" type="password"
                                               id="password" name="password" autocomplete="new-password" required>
                                        <button class="pw-toggle" type="button" data-password-toggle="password">Show</button>
                                    </div>
                                    <?php if (isset($errors['password'])): ?><span class="field__error"><?= e($errors['password']) ?></span><?php endif; ?>
                                </div>

                                <div class="field">
                                    <label class="field__label" for="password_confirmation">Confirm new password <span class="req">*</span></label>
                                    <input class="input <?= isset($errors['password_confirmation']) ? 'input--error' : '' ?>" type="password"
                                           id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                                    <?php if (isset($errors['password_confirmation'])): ?><span class="field__error"><?= e($errors['password_confirmation']) ?></span><?php endif; ?>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button class="btn btn--primary" type="submit"><?= icon('lock', 17) ?> Update password</button>
                                <a class="btn btn--ghost" href="<?= url('/account') ?>">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
