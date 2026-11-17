<?php

use App\Core\Auth;
use App\Core\View;
?>
<div class="admin-page-head">
    <div>
        <h1>Customers &amp; staff</h1>
        <p><?= (int) $total ?> account<?= (int) $total === 1 ? '' : 's' ?>. Promote a customer to administrator to give full store access.</p>
    </div>
</div>

<form class="admin-filterbar" method="get" action="<?= url('/admin/users') ?>">
    <input class="input" type="search" name="q" value="<?= e($search) ?>" placeholder="Search by name or email&hellip;" aria-label="Search users">
    <button class="btn btn--outline btn--sm" type="submit"><?= icon('search', 15) ?> Search</button>
    <?php if ($search !== ''): ?>
        <a class="btn btn--ghost btn--sm" href="<?= url('/admin/users') ?>">Reset</a>
    <?php endif; ?>
</form>

<div class="panel">
    <div class="panel__body panel__body--flush">
        <?php if ($users === []): ?>
            <div class="empty" style="border:0;border-radius:0;padding:44px 20px">
                <span class="empty__icon"><?= icon('users', 26) ?></span>
                <div class="empty__title">No accounts found</div>
                <p class="empty__text">Try a different search term.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th>Account</th><th>Contact</th><th>Joined</th><th>Last sign-in</th><th>Role</th><th>Access</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $account): ?>
                            <?php $isSelf = (int) $account['id'] === (int) Auth::id(); ?>
                            <tr>
                                <td>
                                    <div class="table__product">
                                        <span class="account-menu__avatar"><?= e(user_initials((string) $account['full_name'])) ?></span>
                                        <span>
                                            <span class="table__name">
                                                <?= e($account['full_name']) ?>
                                                <?php if ($isSelf): ?><span class="badge badge--muted">You</span><?php endif; ?>
                                            </span>
                                            <span class="table__sub"><?= e($account['email']) ?></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="muted">
                                    <?= e($account['phone'] ?: '—') ?><br>
                                    <span class="table__sub"><?= e($account['city'] ?: 'No city') ?></span>
                                </td>
                                <td class="muted"><?= e(date('d M Y', strtotime((string) $account['created_at']))) ?></td>
                                <td class="muted">
                                    <?= $account['last_login_at'] ? e(date('d M Y', strtotime((string) $account['last_login_at']))) : 'Never' ?>
                                </td>
                                <td>
                                    <form method="post" action="<?= url('/admin/users/' . $account['id'] . '/role') ?>" class="flex" style="gap:6px">
                                        <?= csrf_field() ?>
                                        <select class="select" name="role" style="width:auto;font-size:.8rem;padding:6px 30px 6px 9px" aria-label="Role for <?= e($account['full_name']) ?>" <?= $isSelf ? 'disabled' : '' ?>>
                                            <option value="customer"<?= selected($account['role'], 'customer') ?>>Customer</option>
                                            <option value="admin"<?= selected($account['role'], 'admin') ?>>Administrator</option>
                                        </select>
                                        <?php if (! $isSelf): ?>
                                            <button class="btn btn--outline btn--sm" type="submit">Save</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                                <td>
                                    <div class="table__actions">
                                        <span class="badge <?= (int) $account['is_active'] === 1 ? 'badge--success' : 'badge--danger' ?>">
                                            <?= (int) $account['is_active'] === 1 ? 'Active' : 'Disabled' ?>
                                        </span>
                                        <?php if (! $isSelf): ?>
                                            <form method="post" action="<?= url('/admin/users/' . $account['id'] . '/active') ?>"
                                                  data-confirm="<?= (int) $account['is_active'] === 1 ? 'Deactivate' : 'Activate' ?> this account?">
                                                <?= csrf_field() ?>
                                                <button class="btn btn--ghost btn--sm" type="submit">
                                                    <?= (int) $account['is_active'] === 1 ? 'Deactivate' : 'Activate' ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($users !== []): ?>
    <?php View::partial('partials/pagination', ['page' => $page, 'pages' => $pages]); ?>
<?php endif; ?>
