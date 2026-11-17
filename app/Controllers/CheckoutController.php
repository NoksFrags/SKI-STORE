<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Validator;
use App\Core\View;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Services\CheckoutService;
use RuntimeException;

final class CheckoutController
{
    public function index(): void
    {
        $items = Cart::items();

        if ($items === []) {
            flash('Your cart is empty - add some gear before checking out.', 'warning');
            redirect('/cart');
        }

        View::render('checkout/index', [
            'title'  => 'Checkout',
            'items'  => $items,
            'totals' => Cart::totals(),
            'user'   => User::find((int) Auth::id()),
        ]);
    }

    public function place(): void
    {
        $user = User::find((int) Auth::id());

        if ($user === null) {
            redirect('/login');
        }

        $validator = (new Validator($_POST))
            ->required('customer_name', 'Full name')
            ->maxLength('customer_name', 120, 'Full name')
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->required('phone', 'Phone')
            ->maxLength('phone', 40, 'Phone')
            ->required('shipping_address', 'Address')
            ->maxLength('shipping_address', 180, 'Address')
            ->required('shipping_city', 'City')
            ->maxLength('shipping_city', 100, 'City')
            ->required('shipping_postal_code', 'Postal code')
            ->maxLength('shipping_postal_code', 20, 'Postal code')
            ->required('shipping_country', 'Country')
            ->maxLength('shipping_country', 80, 'Country')
            ->maxLength('notes', 500, 'Order notes');

        $paymentMethod = (string) ($_POST['payment_method'] ?? '');

        if (! array_key_exists($paymentMethod, Order::PAYMENT_METHODS)) {
            $validator->add('payment_method', 'Choose a payment method.');
        }

        if ($validator->fails()) {
            remember_old_input($_POST, $validator->errors());
            redirect('/checkout');
        }

        try {
            $result = CheckoutService::place($user, [
                'customer_name'        => trim((string) $_POST['customer_name']),
                'email'                => trim((string) $_POST['email']),
                'phone'                => trim((string) $_POST['phone']),
                'shipping_address'     => trim((string) $_POST['shipping_address']),
                'shipping_city'        => trim((string) $_POST['shipping_city']),
                'shipping_postal_code' => trim((string) $_POST['shipping_postal_code']),
                'shipping_country'     => trim((string) $_POST['shipping_country']),
                'payment_method'       => $paymentMethod,
                'notes'                => trim((string) ($_POST['notes'] ?? '')),
            ]);
        } catch (RuntimeException $exception) {
            flash($exception->getMessage(), 'error');
            redirect('/checkout');
        }

        flash('Thank you! Your order ' . $result['order_number'] . ' has been placed.');

        redirect('/checkout/success/' . $result['order_id']);
    }

    public function success(array $params): void
    {
        $order = Order::findForUser((int) ($params['id'] ?? 0), (int) Auth::id());

        if ($order === null) {
            abort(404);
        }

        View::render('checkout/success', [
            'title' => 'Order confirmed',
            'order' => $order,
            'items' => Order::items((int) $order['id']),
        ]);
    }
}
