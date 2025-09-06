<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Deposit;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\ConfirmedOrder;
use App\Models\Order;

class StripeController extends Controller
{
    /**
     * Create a Stripe Checkout session for multiple items and save to MySQL.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCheckoutSession(Request $request)
    {
        $user = Auth::user(); // ensure user is authenticated

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $items = $request->input('items', []);

        if (empty($items)) {
            return response()->json(['error' => 'No items provided'], 422);
        }

        $line_items = [];
        $totalAmountBDT = 0;
        $totalAmountPaisa = 0;

        foreach ($items as $index => $item) {
            // Validate input with amount as raw BDT decimal
            $validator = Validator::make($item, [
                'product_name' => 'required|string',
                'amount' => 'required|numeric|min:0.01',  // raw BDT amount minimum 0.01
                'quantity' => 'integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => "Invalid input for item #$index",
                    'messages' => $validator->errors()
                ], 422);
            }

            $quantity = $item['quantity'] ?? 1;
            $amountBDT = (float) $item['amount'];
            $amountPaisa = (int) round($amountBDT * 100); // convert BDT to paisa for Stripe

            $line_items[] = [
                'price_data' => [
                    'currency' => 'bdt',
                    'unit_amount' => $amountPaisa,
                    'product_data' => [
                        'name' => $item['product_name'],
                    ],
                ],
                'quantity' => $quantity
            ];

            $totalAmountBDT += $amountBDT * $quantity;
            $totalAmountPaisa += $amountPaisa * $quantity;
        }

        // Minimum amount check in BDT
        if ($totalAmountBDT < 20) {
            return response()->json([
                'error' => 'Minimum amount not met',
                'message' => 'Total amount must be at least ৳20'
            ], 422);
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Create Stripe checkout session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => 'http://localhost:5173/success',
                'cancel_url' => 'http://localhost:5173/cancel',
            ]);

            // Save deposit in DB — store raw BDT amount (for easier display)
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'amount' => $totalAmountBDT,  // raw BDT
                'session_id' => $session->id,
                'status' => 'completed',
            ]);

            // Save order items with raw BDT unit price
            foreach ($items as $item) {
                OrderItem::create([
                    'deposit_id' => $deposit->id,
                    'product_name' => $item['product_name'],
                    'unit_price' => (float) $item['amount'],  // raw BDT
                    'quantity' => $item['quantity'] ?? 1,
                ]);
            }

            // ====== NEW ORDER + CONFIRMED ORDER CREATION ======
            $cartItems = Cart::where('user_id', $user->id)->with('product')->get();
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

            if (!empty($orderIds)) {
                ConfirmedOrder::create([
                    'order_id' => $orderIds[0], // you can change to last() or all if needed
                    'user_id' => $user->id,
                    'payment_status' => 'pending',
                    'payment_method' => $request->payment_method ?? 'card',
                    'delivery_address' => $request->delivery_address ?? 'N/A',
                    'delivery_status' => 'pending',
                ]);
            }

            // Clear user's cart after creating order
            Cart::where('user_id', $user->id)->delete();

            return response()->json([
                'id' => $session->id,
                'url' => $session->url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Stripe error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
