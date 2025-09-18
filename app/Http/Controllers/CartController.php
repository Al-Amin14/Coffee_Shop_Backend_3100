<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController  extends Controller
{
    // 1️⃣ Add to cart
    public function addToCart(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = Cart::where('user_id', $request->user_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {
            $cart->quantity += 1;
            $cart->save();
        } else {
            $cart = Cart::create([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'quantity' => 1,
                'unit_price' => $request->unit_price,
                'image_path' => $request->image_path,
                'product_name' => $request->product_name,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cart' => $cart,
        ]);
    }

    // 2️⃣ Increment quantity
    public function increment(Request $request, $cartId)
    {
        $cart = Cart::findOrFail($cartId);
        $cart->quantity += 1;
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'Quantity increased',
            'cart' => $cart,
        ]);
    }

    // 3️⃣ Decrement quantity
    public function decrement(Request $request, $cartId)
    {
        $cart = Cart::findOrFail($cartId);

        if ($cart->quantity > 1) {
            $cart->quantity -= 1;
            $cart->save();
        } else {
            $cart->delete();
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Quantity decreased',
            'cart' => $cart,
        ]);
    }
    public function getUserCart($userId)
    {
        $carts = Cart::with('product') // eager load product details
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'User cart fetched successfully',
            'cart' => $carts,
        ]);
    }
}