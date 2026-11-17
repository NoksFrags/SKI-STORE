<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Cart;

final class CartController
{
    public function index(): void
    {
        View::render('cart/index', [
            'title'  => 'Shopping cart',
            'items'  => Cart::items(),
            'totals' => Cart::totals(),
        ]);
    }

    public function add(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);

        if ($productId < 1) {
            abort(404);
        }

        $error = Cart::add($productId, $quantity);

        if ($error !== null) {
            flash($error, 'error');
        } else {
            flash('Added to your cart.');
        }

        back();
    }

    public function update(): void
    {
        $quantities = $_POST['quantities'] ?? [];

        if (is_array($quantities)) {
            foreach ($quantities as $productId => $quantity) {
                if (! is_numeric($productId)) {
                    continue;
                }

                $message = Cart::updateQuantity((int) $productId, (int) $quantity);

                if (is_string($message)) {
                    flash($message, 'warning');
                }
            }
        }

        flash('Your cart has been updated.');
        redirect('/cart');
    }

    public function remove(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);

        if ($productId > 0) {
            Cart::remove($productId);
            flash('Item removed from your cart.');
        }

        redirect('/cart');
    }

    public function clear(): void
    {
        Cart::clear();
        flash('Your cart is now empty.');

        redirect('/cart');
    }
}
