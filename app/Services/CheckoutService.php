<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use RuntimeException;
use Throwable;

final class CheckoutService
{

    public static function place(array $user, array $data): array
    {
        $items = Cart::items();

        if ($items === []) {
            throw new RuntimeException('Your cart is empty.');
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            $productIds = array_map(static fn (array $item): int => (int) $item['product_id'], $items);
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));

            $statement = $pdo->prepare(
                'SELECT id, name, price, stock, is_active FROM products WHERE id IN (' . $placeholders . ') FOR UPDATE'
            );
            $statement->execute($productIds);

            $locked = [];

            foreach ($statement->fetchAll() as $row) {
                $locked[(int) $row['id']] = $row;
            }

            $subtotal = 0.0;
            $lines = [];

            foreach ($items as $item) {
                $product = $locked[(int) $item['product_id']] ?? null;

                if ($product === null || (int) $product['is_active'] !== 1) {
                    throw new RuntimeException('One of the products in your cart is no longer available.');
                }

                $quantity = (int) $item['quantity'];

                if ((int) $product['stock'] < $quantity) {
                    throw new RuntimeException(
                        'Not enough stock for "' . $product['name'] . '". Only ' . (int) $product['stock'] . ' left.'
                    );
                }

                $lineTotal = round((float) $product['price'] * $quantity, 2);
                $subtotal += $lineTotal;

                $lines[] = [
                    'product_id' => (int) $product['id'],
                    'name'       => (string) $product['name'],
                    'price'      => (float) $product['price'],
                    'quantity'   => $quantity,
                    'total'      => $lineTotal,
                ];
            }

            $subtotal = round($subtotal, 2);
            $shipping = $subtotal >= (float) config('app.free_shipping_from') ? 0.0 : (float) config('app.shipping_flat');
            $total = round($subtotal + $shipping, 2);
            $number = Order::generateNumber();

            $orderId = Order::create([
                'order_number'        => $number,
                'user_id'             => (int) $user['id'],
                'status'              => 'pending',
                'payment_method'      => $data['payment_method'],
                'subtotal'            => $subtotal,
                'shipping_cost'       => $shipping,
                'total'               => $total,
                'customer_name'       => $data['customer_name'],
                'email'               => $data['email'],
                'phone'               => $data['phone'],
                'shipping_address'    => $data['shipping_address'],
                'shipping_city'       => $data['shipping_city'],
                'shipping_postal_code' => $data['shipping_postal_code'],
                'shipping_country'    => $data['shipping_country'],
                'notes'               => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                Order::addItem($orderId, $line);

                if (! Product::decreaseStock($line['product_id'], $line['quantity'])) {
                    throw new RuntimeException('Stock changed while placing your order. Please review the cart and try again.');
                }
            }

            Cart::clear();
            $pdo->commit();

            return ['order_id' => $orderId, 'order_number' => $number];
        } catch (Throwable $exception) {
            $pdo->rollBack();

            throw $exception;
        }
    }
}
