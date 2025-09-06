<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    private function response(bool $success, string $message, $data = null)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'cart'    => $data,
        ]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'user_id'      => 'required|exists:users,id',
            'product_id'   => 'required|exists:products,id',
            'unit_price'   => 'required|numeric',
            'image_path'   => 'nullable|string',
            'product_name' => 'required|string|max:255',
        ]);

        $cart = Cart::firstOrCreate(
            ['user_id' => $validated['user_id'], 'product_id' => $validated['product_id']],
            [
                'quantity'     => 1,
                'unit_price'   => $validated['unit_price'],
                'image_path'   => $validated['image_path'] ?? null,
                'product_name' => $validated['product_name'],
            ]
        );

        if (!$cart->wasRecentlyCreated) {
            $cart->increment('quantity');
        }

        return $this->response(true, 'Product added to cart', $cart);
    }

    public function increment($cartId)
    {
        $cart = Cart::findOrFail($cartId);
        $cart->increment('quantity');
        return $this->response(true, 'Quantity increased', $cart);
    }

    public function decrement($cartId)
    {
        $cart = Cart::findOrFail($cartId);

        if ($cart->quantity > 1) {
            $cart->decrement('quantity');
            return $this->response(true, 'Quantity decreased', $cart);
        }

        $cart->delete();
        return $this->response(true, 'Item removed from cart');
    }

    public function getUserCart($userId)
    {
        $carts = Cart::with('product')->where('user_id', $userId)->get();
        return $this->response(true, 'User cart fetched successfully', $carts);
    }
}
