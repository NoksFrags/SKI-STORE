<?php

declare(strict_types=1);

use App\Core\Flash;

/**
 * Read a value from the .env file.
 */
function env(string $key, ?string $default = null): ?string
{
    $value = $GLOBALS['__env'][$key] ?? null;

    return ($value === null || $value === '') ? $default : (string) $value;
}

/**
 * Read configuration with dot notation, e.g. config('app.name').
 */
function config(string $key = '', mixed $default = null): mixed
{
    static $config = null;

    if ($config === null) {
        $config = [
            'app'      => require CONFIG_PATH . '/app.php',
            'database' => require CONFIG_PATH . '/database.php',
        ];
    }

    if ($key === '') {
        return $config;
    }

    $value = $config;

    foreach (explode('.', $key) as $segment) {
        if (! is_array($value) || ! array_key_exists($segment, $value)) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}

/**
 * Escape output for safe HTML rendering.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Base URL path of the project, detected from the executing script.
 */
function base_url(): string
{
    static $base = null;

    if ($base !== null) {
        return $base;
    }

    $configured = (string) config('app.url', '');

    if ($configured !== '') {
        return $base = rtrim($configured, '/');
    }

    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));

    if (str_ends_with($script, '/public/index.php')) {
        return $base = substr($script, 0, -strlen('/public/index.php'));
    }

    if (str_ends_with($script, '/index.php')) {
        return $base = substr($script, 0, -strlen('/index.php'));
    }

    return $base = '';
}

/**
 * Directory the front controller lives in, used for static assets.
 */
function asset_base(): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));

    if (str_ends_with($script, '/index.php')) {
        return substr($script, 0, -strlen('/index.php'));
    }

    return '';
}

function url(string $path = '/'): string
{
    $path = '/' . ltrim($path, '/');

    return base_url() . ($path === '/' ? '/' : rtrim($path, '/'));
}

function public_asset(string $path): string
{
    return asset_base() . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return public_asset('assets/' . ltrim($path, '/'));
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function back(): never
{
    $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');

    if ($referer !== '') {
        header('Location: ' . $referer);
        exit;
    }

    redirect('/');
}

/**
 * Render an error page and stop the request.
 */
function abort(int $code, ?string $message = null): never
{
    http_response_code($code);

    $titles = [
        403 => 'Access denied',
        404 => 'Page not found',
        405 => 'Method not allowed',
        419 => 'Page expired',
        500 => 'Something went wrong',
    ];

    $title = $titles[$code] ?? 'Unexpected error';

    $message ??= match ($code) {
        403 => 'You do not have permission to view this page.',
        404 => 'The page you are looking for does not exist or has been moved.',
        405 => 'That action is not allowed on this address.',
        419 => 'Your session expired. Please go back and try again.',
        default => 'The store ran into an unexpected problem. Please try again in a moment.',
    };

    try {
        \App\Core\View::render('errors/error', compact('code', 'title', 'message'), 'layouts/error');
    } catch (\Throwable) {
        echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>' . $code . '</title>';
        echo '<body style="font-family:system-ui,sans-serif;padding:64px;max-width:640px;margin:auto;color:#0f172a">';
        echo '<h1 style="font-size:48px;margin:0 0 8px">' . $code . '</h1>';
        echo '<h2 style="margin:0 0 16px">' . e($title) . '</h2>';
        echo '<p style="line-height:1.6">' . e($message) . '</p>';
        echo '<p><a href="' . e(url('/')) . '">&larr; Back to the store</a></p></body></html>';
    }

    exit;
}

/**
 * Current request path with the project base removed.
 */
function request_path(): string
{
    $path = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
    $path = rawurldecode($path);
    $base = base_url();

    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
    }

    if (str_starts_with($path, '/public/index.php')) {
        $path = substr($path, strlen('/public/index.php'));
    }

    $path = '/' . ltrim($path, '/');

    return rtrim($path, '/') === '' ? '/' : rtrim($path, '/');
}

function request_input(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function format_number(float|int|string $value, ?int $decimals = null): string
{
    $decimals ??= (int) config('app.currency_decimals', 2);

    return number_format((float) $value, $decimals, ',', '.');
}

function money(float|int|string $value): string
{
    return format_number($value) . ' ' . (string) config('app.currency', 'KM');
}

function flash(string $message, string $type = 'success'): void
{
    Flash::add($type, $message);
}

/** @return array<string, string> */
function errors(): array
{
    $errors = $_SESSION['_errors'] ?? [];

    return is_array($errors) ? $errors : [];
}

function error(string $field): ?string
{
    return errors()[$field] ?? null;
}

function old(string $field, mixed $default = ''): string
{
    $value = $_SESSION['_old'][$field] ?? $default;

    return is_string($value) ? $value : (string) $value;
}

function forget_transient_input(): void
{
    unset($_SESSION['_errors'], $_SESSION['_old']);
}

function remember_old_input(array $input, array $errors = []): void
{
    unset($input['_token'], $input['password'], $input['password_confirmation'], $input['current_password']);

    $_SESSION['_old'] = $input;

    if ($errors !== []) {
        $_SESSION['_errors'] = $errors;
    }
}

function csrf_field(): string
{
    return \App\Core\Csrf::field();
}

function is_active(string $prefix): bool
{
    $path = request_path();

    if ($prefix === '/') {
        return $path === '/';
    }

    return $path === $prefix || str_starts_with($path, rtrim($prefix, '/') . '/');
}

function selected(mixed $value, mixed $expected): string
{
    return (string) $value === (string) $expected ? ' selected' : '';
}

function checked(bool $condition): string
{
    return $condition ? ' checked' : '';
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';

    return trim($value, '-') ?: 'item-' . random_int(1000, 9999);
}

function excerpt(?string $text, int $limit = 120): string
{
    $text = trim(strip_tags((string) $text));

    if (mb_strlen($text) <= $limit) {
        return $text;
    }

    return rtrim(mb_substr($text, 0, $limit)) . '…';
}

/**
 * Resolve a stored product image to a public URL.
 */
function product_image(?string $image, bool $withFallback = true): string
{
    $image = trim((string) $image);

    if ($image !== '' && (str_starts_with($image, 'http://') || str_starts_with($image, 'https://'))) {
        return $image;
    }

    if ($image !== '' && is_file(PUBLIC_PATH . '/' . ltrim($image, '/'))) {
        return public_asset($image);
    }

    return $withFallback ? public_asset('assets/img/products/placeholder.svg') : '';
}

/**
 * Snap any requested icon size to the shared 4px scale (12, 16, 20, 24, 28)
 * so stroke weight and optical spacing stay consistent across the UI.
 */
function icon_size(int|string|null $size, int $fallback = 20): int
{
    $requested = is_numeric($size) ? (int) $size : $fallback;
    $scale = [12, 16, 20, 24, 28];

    foreach ($scale as $step) {
        if ($requested <= $step) {
            return $step;
        }
    }

    return $scale[count($scale) - 1];
}

function user_initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $initials = '';

    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }

    return $initials !== '' ? $initials : 'U';
}

/**
 * Client IP is used for lightweight logging of login attempts.
 */
function client_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

/**
 * Cached navigation categories shared by the storefront layout.
 */
function nav_categories(): array
{
    static $categories = null;

    return $categories ??= \App\Models\Category::all();
}

function cart_quantity(): int
{
    static $count = null;

    return $count ??= \App\Models\Cart::quantityCount();
}

/**
 * @return array<int, int>
 */
function wishlist_ids(): array
{
    static $ids = null;

    if ($ids === null) {
        $ids = \App\Core\Auth::check()
            ? \App\Models\Wishlist::ids((int) \App\Core\Auth::id())
            : [];
    }

    return $ids;
}

function in_wishlist(int $productId): bool
{
    return in_array($productId, wishlist_ids(), true);
}

function wishlist_count(): int
{
    return count(wishlist_ids());
}

/**
 * Build a URL for the current page with query string overrides.
 */
function query_url(array $overrides = []): string
{
    $params = array_merge($_GET, $overrides);

    foreach ($params as $key => $value) {
        if ($value === null || $value === '') {
            unset($params[$key]);
        }
    }

    $query = http_build_query($params);

    return request_path() . ($query !== '' ? '?' . $query : '');
}

function icon(string $name, int $size = 20): string
{
    return \App\Core\View::capture('partials/icon', ['name' => $name, 'size' => $size]);
}

function order_status_class(string $status): string
{
    return match ($status) {
        'pending'    => 'badge--warning',
        'processing' => 'badge--info',
        'shipped'    => 'badge--brand',
        'completed'  => 'badge--success',
        'cancelled'  => 'badge--danger',
        default      => 'badge--muted',
    };
}
