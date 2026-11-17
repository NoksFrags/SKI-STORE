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
                    <h1>Profile details</h1>
                    <p>Used to prefill checkout and keep you reachable about deliveries.</p>
                </div>

                <div class="panel">
                    <div class="panel__head">
                        <h2>Personal &amp; delivery details</h2>
                        <span class="badge badge--muted"><?= e($user['role'] === 'admin' ? 'Administrator' : 'Customer') ?></span>
                    </div>
                    <div class="panel__body">
                        <form method="post" action="<?= url('/account/profile') ?>">
                            <?= csrf_field() ?>

                            <div class="form-grid">
                                <div class="field">
                                    <label class="field__label" for="full_name">Full name <span class="req">*</span></label>
                                    <input class="input <?= isset($errors['full_name']) ? 'input--error' : '' ?>" type="text" id="full_name"
                                           name="full_name" value="<?= e(old('full_name', (string) $user['full_name'])) ?>" autocomplete="name" required>
                                    <?php if (isset($errors['full_name'])): ?><span class="field__error"><?= e($errors['full_name']) ?></span><?php endif; ?>
                                </div>

                                <div class="field">
                                    <label class="field__label" for="email">Email <span class="req">*</span></label>
                                    <input class="input <?= isset($errors['email']) ? 'input--error' : '' ?>" type="email" id="email"
                                           name="email" value="<?= e(old('email', (string) $user['email'])) ?>" autocomplete="email" required>
                                    <?php if (isset($errors['email'])): ?><span class="field__error"><?= e($errors['email']) ?></span><?php endif; ?>
                                </div>

                                <div class="field">
                                    <label class="field__label" for="phone">Phone</label>
                                    <input class="input <?= isset($errors['phone']) ? 'input--error' : '' ?>" type="tel" id="phone"
                                           name="phone" value="<?= e(old('phone', (string) ($user['phone'] ?? ''))) ?>" autocomplete="tel">
                                    <?php if (isset($errors['phone'])): ?><span class="field__error"><?= e($errors['phone']) ?></span><?php endif; ?>
                                </div>

                                <div class="field">
                                    <label class="field__label" for="country">Country</label>
                                    <select class="select" id="country" name="country">
                                        <?php foreach (['Bosnia and Herzegovina', 'Croatia', 'Serbia', 'Montenegro', 'Slovenia'] as $country): ?>
                                            <option value="<?= e($country) ?>"<?= selected(old('country', (string) ($user['country'] ?? 'Bosnia and Herzegovina')), $country) ?>><?= e($country) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="field field--full">
                                    <label class="field__label" for="address">Street address</label>
                                    <input class="input <?= isset($errors['address']) ? 'input--error' : '' ?>" type="text" id="address"
                                           name="address" value="<?= e(old('address', (string) ($user['address'] ?? ''))) ?>" autocomplete="street-address">
                                    <?php if (isset($errors['address'])): ?><span class="field__error"><?= e($errors['address']) ?></span><?php endif; ?>
                                </div>

                                <div class="field">
                                    <label class="field__label" for="city">City</label>
                                    <input class="input <?= isset($errors['city']) ? 'input--error' : '' ?>" type="text" id="city"
                                           name="city" value="<?= e(old('city', (string) ($user['city'] ?? ''))) ?>" autocomplete="address-level2">
                                    <?php if (isset($errors['city'])): ?><span class="field__error"><?= e($errors['city']) ?></span><?php endif; ?>
                                </div>

                                <div class="field">
                                    <label class="field__label" for="postal_code">Postal code</label>
                                    <input class="input <?= isset($errors['postal_code']) ? 'input--error' : '' ?>" type="text" id="postal_code"
                                           name="postal_code" value="<?= e(old('postal_code', (string) ($user['postal_code'] ?? ''))) ?>" autocomplete="postal-code">
                                    <?php if (isset($errors['postal_code'])): ?><span class="field__error"><?= e($errors['postal_code']) ?></span><?php endif; ?>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button class="btn btn--primary" type="submit"><?= icon('check', 17) ?> Save changes</button>
                                <a class="btn btn--ghost" href="<?= url('/account') ?>">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
