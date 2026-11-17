<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Models\Product;
use App\Models\Wishlist;

final class WishlistController
{
    public function index(): void
    {
        View::render('wishlist/index', [
            'title' => 'My wishlist',
            'items' => Wishlist::items((int) Auth::id()),
        ]);
    }

    public function toggle(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);

        if ($productId < 1 || Product::find($productId) === null) {
            abort(404);
        }

        $added = Wishlist::toggle((int) Auth::id(), $productId);

        flash($added ? 'Saved to your wishlist.' : 'Removed from your wishlist.', $added ? 'success' : 'info');

        back();
    }

    public function remove(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);

        if ($productId > 0) {
            Wishlist::remove((int) Auth::id(), $productId);
            flash('Removed from your wishlist.', 'info');
        }

        back();
    }
}
