<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Auth;
use App\Core\Database;

final class Cart
{
    private static ?array $row = null;

    private static ?array $items = null;

    public static function reset(): void
    {
        self::$row = null;
        self::$items = null;
    }

    private static function findRow(): ?array
    {
        if (self::$row !== null) {
            return self::$row;
        }

        $pdo = Database::pdo();
        $userId = Auth::id();

        if ($userId !== null) {
            $statement = $pdo->prepare('SELECT * FROM carts WHERE user_id = ? ORDER BY id LIMIT 1');
            $statement->execute([$userId]);
        } else {
            $token = $_SESSION['cart_token'] ?? null;

            if (! is_string($token) || $token === '') {
                return null;
            }

            $statement = $pdo->prepare('SELECT * FROM carts WHERE session_token = ? AND user_id IS NULL ORDER BY id LIMIT 1');
            $statement->execute([$token]);
        }

        $row = $statement->fetch();

        return $row ?: null;
    }

    private static function ensureRow(): array
    {
        $row = self::findRow();

        if ($row !== null) {
            return $row;
        }

        $pdo = Database::pdo();
        $userId = Auth::id();

        if ($userId !== null) {
            $pdo->prepare('INSERT INTO carts (user_id) VALUES (?)')->execute([$userId]);
        } else {
            if (empty($_SESSION['cart_token'])) {
                $_SESSION['cart_token'] = bin2hex(random_bytes(16));
            }

            $pdo->prepare('INSERT INTO carts (session_token) VALUES (?)')->execute([(string) $_SESSION['cart_token']]);
        }

        return self::$row = ['id' => (int) $pdo->lastInsertId()];
    }

    public static function items(): array
    {
        if (self::$items !== null) {
            return self::$items;
        }

        $row = self::findRow();

        if ($row === null) {
            return self::$items = [];
        }

        $statement = Database::pdo()->prepare(
            'SELECT ci.product_id, ci.quantity, p.name, p.slug, p.price, p.stock, p.image, p.brand, p.is_active
             FROM cart_items ci
             INNER JOIN products p ON p.id = ci.product_id
             WHERE ci.cart_id = ?
             ORDER BY ci.id ASC'
        );
        $statement->execute([(int) $row['id']]);

        $items = $statement->fetchAll();

        foreach ($items as $index => $item) {
            $items[$index]['line_total'] = round((float) $item['price'] * (int) $item['quantity'], 2);
        }

        return self::$items = $items;
    }

    public static function quantityCount(): int
    {
        $userId = Auth::id();
        $pdo = Database::pdo();

        if ($userId !== null) {
            $statement = $pdo->prepare(
                'SELECT COALESCE(SUM(ci.quantity), 0) FROM cart_items ci
                 INNER JOIN carts c ON c.id = ci.cart_id WHERE c.user_id = ?'
            );
            $statement->execute([$userId]);
        } else {
            $token = $_SESSION['cart_token'] ?? null;

            if (! is_string($token) || $token === '') {
                return 0;
            }

            $statement = $pdo->prepare(
                'SELECT COALESCE(SUM(ci.quantity), 0) FROM cart_items ci
                 INNER JOIN carts c ON c.id = ci.cart_id WHERE c.session_token = ? AND c.user_id IS NULL'
            );
            $statement->execute([$token]);
        }

        return (int) $statement->fetchColumn();
    }

    public static function add(int $productId, int $quantity = 1): ?string
    {
        $quantity = max(1, min(20, $quantity));
        $product = Product::find($productId);

        if ($product === null || (int) $product['is_active'] !== 1) {
            return 'That product is not available anymore.';
        }

        $stock = (int) $product['stock'];

        if ($stock < 1) {
            return 'Sorry, "' . $product['name'] . '" is out of stock.';
        }

        $cart = self::ensureRow();
        $pdo = Database::pdo();

        $statement = $pdo->prepare('SELECT quantity FROM cart_items WHERE cart_id = ? AND product_id = ? LIMIT 1');
        $statement->execute([(int) $cart['id'], $productId]);
        $existing = $statement->fetchColumn();
        $existing = $existing === false ? 0 : (int) $existing;
        $newQuantity = min($stock, $existing + $quantity);

        if ($existing + $quantity > $stock) {
            $message = 'Only ' . $stock . ' left in stock - your cart was adjusted.';
        } else {
            $message = null;
        }

        $pdo->prepare(
            'INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE quantity = ?'
        )->execute([(int) $cart['id'], $productId, $newQuantity, $newQuantity]);

        self::$items = null;

        return $message;
    }

    public static function updateQuantity(int $productId, int $quantity): ?string
    {
        $row = self::findRow();

        if ($row === null) {
            return null;
        }

        $pdo = Database::pdo();

        if ($quantity <= 0) {
            self::remove($productId);

            return null;
        }

        $product = Product::find($productId);

        if ($product === null) {
            return 'That product is not available anymore.';
        }

        $quantity = min($quantity, max(1, (int) $product['stock']));
        $message = $quantity < (int) ($_POST['quantity'] ?? $quantity) ? 'Quantity limited to available stock.' : null;

        $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?')
            ->execute([$quantity, (int) $row['id'], $productId]);

        self::$items = null;

        return $message;
    }

    public static function remove(int $productId): void
    {
        $row = self::findRow();

        if ($row !== null) {
            Database::pdo()->prepare('DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?')
                ->execute([(int) $row['id'], $productId]);
        }

        self::$items = null;
    }

    public static function clear(): void
    {
        $row = self::findRow();

        if ($row !== null) {
            Database::pdo()->prepare('DELETE FROM cart_items WHERE cart_id = ?')->execute([(int) $row['id']]);
        }

        self::$items = null;
    }

    public static function mergeGuestCart(int $userId): void
    {
        $token = $_SESSION['cart_token'] ?? null;

        self::reset();

        if (! is_string($token) || $token === '') {
            return;
        }

        $pdo = Database::pdo();

        $statement = $pdo->prepare('SELECT * FROM carts WHERE session_token = ? AND user_id IS NULL ORDER BY id LIMIT 1');
        $statement->execute([$token]);
        $guestCart = $statement->fetch();

        unset($_SESSION['cart_token']);

        if (! $guestCart) {
            return;
        }

        $guestCartId = (int) $guestCart['id'];

        $statement = $pdo->prepare('SELECT id FROM carts WHERE user_id = ? ORDER BY id LIMIT 1');
        $statement->execute([$userId]);
        $userCartId = $statement->fetchColumn();

        if ($userCartId === false) {
            $pdo->prepare('INSERT INTO carts (user_id) VALUES (?)')->execute([$userId]);
            $userCartId = (int) $pdo->lastInsertId();
        }

        $userCartId = (int) $userCartId;

        $statement = $pdo->prepare(
            'SELECT ci.product_id, ci.quantity, p.stock FROM cart_items ci
             INNER JOIN products p ON p.id = ci.product_id
             WHERE ci.cart_id = ?'
        );
        $statement->execute([$guestCartId]);

        foreach ($statement->fetchAll() as $item) {
            $stock = max(1, (int) $item['stock']);

            $check = $pdo->prepare('SELECT quantity FROM cart_items WHERE cart_id = ? AND product_id = ? LIMIT 1');
            $check->execute([$userCartId, (int) $item['product_id']]);
            $existing = $check->fetchColumn();

            if ($existing === false) {
                $pdo->prepare('INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)')
                    ->execute([$userCartId, (int) $item['product_id'], min($stock, (int) $item['quantity'])]);
            } else {
                $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?')
                    ->execute([min($stock, (int) $existing + (int) $item['quantity']), $userCartId, (int) $item['product_id']]);
            }
        }

        $pdo->prepare('DELETE FROM carts WHERE id = ?')->execute([$guestCartId]);

        self::reset();
    }

    public static function totals(): array
    {
        $items = self::items();
        $subtotal = 0.0;

        foreach ($items as $item) {
            $subtotal += (float) $item['price'] * (int) $item['quantity'];
        }

        $subtotal = round($subtotal, 2);
        $freeFrom = (float) config('app.free_shipping_from');
        $shipping = ($subtotal <= 0 || $subtotal >= $freeFrom) ? 0.0 : (float) config('app.shipping_flat');

        return [
            'subtotal'           => $subtotal,
            'shipping'           => round($shipping, 2),
            'total'              => round($subtotal + $shipping, 2),
            'count'              => array_sum(array_map(static fn (array $item): int => (int) $item['quantity'], $items)),
            'free_shipping_gap'  => max(0.0, round($freeFrom - $subtotal, 2)),
            'free_shipping_from' => $freeFrom,
        ];
    }
}
