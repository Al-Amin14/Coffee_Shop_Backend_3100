<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfirmedOrder;
use App\Models\Order;
use App\Models\Cart;

class ConfirmController extends Controller
{
    public function checkout(Request $request)
    {

        $user = auth()->user();

        $request->validate([
            'payment_method' => 'required|in:cod,card,bkash,nagad,rocket',
            'delivery_address' => 'required|string',
        ]);

        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty!',
            ], 400);
        }

        $orderIds = [];
        foreach ($cartItems as $cartItem) {
            $order = Order::create([
                'user_id' => $user->id,
                'product_name' => $cartItem->product->product_name ?? 'Unknown Product',
                'quantity' => $cartItem->quantity,
                'total_price' => $cartItem->quantity * $cartItem->unit_price,
                'image_path' => $cartItem->product->image_path ?? null,
                'status' => 'completed',
            ]);
            $orderIds[] = $order->id;
        }

        $confirmedOrder = ConfirmedOrder::create([
            'order_id' => $orderIds[0], // or store last/any order id
            'user_id' => $user->id,
            'payment_status' => 'pending',
            'payment_method' => $request->payment_method,
            'delivery_address' => $request->delivery_address,
            'delivery_status' => 'pending',
        ]);

        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order confirmed successfully!',
            'confirmed_id' => $confirmedOrder->id,
            'order_ids' => $orderIds,
        ]);
    }

    public function confirm(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cod,card,bkash,nagad,rocket',
            'delivery_address' => 'required|string',
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found!',
            ], 404);
        }

        $order->update(['status' => 'completed']);

        $newConfirmedOrder = ConfirmedOrder::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'payment_status' => 'paid', // mark as paid, can also be 'pending'
            'payment_method' => $request->payment_method,
            'delivery_address' => $request->delivery_address,
            'delivery_status' => 'pending', // start as pending, can be updated later
        ]);

        return response()->json([
            'success' => true,
            'message' => 'New confirmed order created successfully!',
            'confirmed_order' => $newConfirmedOrder,
        ]);
    }

}