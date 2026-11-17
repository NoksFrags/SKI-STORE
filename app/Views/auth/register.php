<?php $errors = errors(); ?>
<div class="auth-card__head">
    <span class="eyebrow">Join the shop</span>
    <h1>Create your account</h1>
    <p>One account for orders, wishlists and faster checkout.</p>
</div>

<form method="post" action="<?= url('/register') ?>">
    <?= csrf_field() ?>

    <div class="form-grid">
        <div class="field">
            <label class="field__label" for="full_name">Full name <span class="req">*</span></label>
            <input class="input <?= isset($errors['full_name']) ? 'input--error' : '' ?>" type="text" id="full_name" name="full_name"
                   value="<?= e(old('full_name')) ?>" autocomplete="name" placeholder="Amina Hodzic" required autofocus>
            <?php if (isset($errors['full_name'])): ?><span class="field__error"><?= e($errors['full_name']) ?></span><?php endif; ?>
        </div>

        <div class="field">
            <label class="field__label" for="email">Email address <span class="req">*</span></label>
            <input class="input <?= isset($errors['email']) ? 'input--error' : '' ?>" type="email" id="email" name="email"
                   value="<?= e(old('email')) ?>" autocomplete="email" placeholder="you@example.com" required>
            <?php if (isset($errors['email'])): ?><span class="field__error"><?= e($errors['email']) ?></span><?php endif; ?>
        </div>

        <div class="form-grid form-grid--tight" style="gap:14px">
            <div class="field">
                <label class="field__label" for="phone">Phone <span class="muted">(optional)</span></label>
                <input class="input" type="tel" id="phone" name="phone" value="<?= e(old('phone')) ?>" autocomplete="tel" placeholder="+387 61 000 000">
            </div>
            <div class="field">
                <label class="field__label" for="city">City <span class="muted">(optional)</span></label>
                <input class="input" type="text" id="city" name="city" value="<?= e(old('city')) ?>" autocomplete="address-level2" placeholder="Sarajevo">
            </div>
        </div>

        <div class="field">
            <label class="field__label" for="password">Password <span class="req">*</span></label>
            <div class="input-group">
                <input class="input <?= isset($errors['password']) ? 'input--error' : '' ?>" type="password" id="password" name="password"
                       autocomplete="new-password" placeholder="At least 8 characters" required>
                <button class="pw-toggle" type="button" data-password-toggle="password">Show</button>
            </div>
            <?php if (isset($errors['password'])): ?><span class="field__error"><?= e($errors['password']) ?></span><?php endif; ?>
        </div>

        <div class="field">
            <label class="field__label" for="password_confirmation">Confirm password <span class="req">*</span></label>
            <input class="input <?= isset($errors['password_confirmation']) ? 'input--error' : '' ?>" type="password"
                   id="password_confirmation" name="password_confirmation" autocomplete="new-password" placeholder="Repeat your password" required>
            <?php if (isset($errors['password_confirmation'])): ?><span class="field__error"><?= e($errors['password_confirmation']) ?></span><?php endif; ?>
        </div>

        <div class="field">
            <label class="checkbox">
                <input type="checkbox" name="terms" value="1" <?= old('terms') !== '' ? 'checked' : '' ?>>
                <span>I agree to the shop terms and the privacy policy for order processing.</span>
            </label>
            <?php if (isset($errors['terms'])): ?><span class="field__error"><?= e($errors['terms']) ?></span><?php endif; ?>
        </div>
    </div>

    <button class="btn btn--primary btn--block btn--lg" type="submit" style="margin-top:20px">
        <?= icon('user', 17) ?> Create account
    </button>
</form>

<div class="auth-card__foot">
    Already have an account? <a href="<?= url('/login') ?>">Sign in</a>
</div>
