<?php $errors = errors(); ?>
<div class="auth-card__head">
    <span class="eyebrow">Welcome back</span>
    <h1>Sign in to your account</h1>
    <p>Track orders, manage your wishlist and check out faster.</p>
</div>

<form method="post" action="<?= url('/login') ?>">
    <?= csrf_field() ?>

    <div class="form-grid">
        <div class="field">
            <label class="field__label" for="email">Email address</label>
            <input class="input <?= isset($errors['email']) ? 'input--error' : '' ?>" type="email" id="email" name="email"
                   value="<?= e(old('email')) ?>" autocomplete="email" placeholder="you@example.com" required autofocus>
            <?php if (isset($errors['email'])): ?><span class="field__error"><?= e($errors['email']) ?></span><?php endif; ?>
        </div>

        <div class="field">
            <label class="field__label" for="password">Password</label>
            <div class="input-group">
                <input class="input <?= isset($errors['password']) ? 'input--error' : '' ?>" type="password" id="password" name="password"
                       autocomplete="current-password" placeholder="Your password" required>
                <button class="pw-toggle" type="button" data-password-toggle="password">Show</button>
            </div>
            <?php if (isset($errors['password'])): ?><span class="field__error"><?= e($errors['password']) ?></span><?php endif; ?>
        </div>
    </div>

    <div class="auth-links" style="margin:18px 0 20px">
        <span class="small muted">Sessions stay signed in on this device.</span>
        <a class="small" href="<?= url('/register') ?>" style="font-weight:700;color:var(--navy)">Need an account?</a>
    </div>

    <button class="btn btn--primary btn--block btn--lg" type="submit"><?= icon('lock', 17) ?> Sign in</button>
</form>

<div class="auth-card__foot">
    New to Ski Store? <a href="<?= url('/register') ?>">Create an account</a>
</div>

<div class="auth-demo">
    <strong>Demo accounts</strong>
    Customer: <code>customer@skistore.test</code> / <code>Customer123!</code><br>
    Admin: <code>admin@skistore.test</code> / <code>Admin123!</code>
</div>
