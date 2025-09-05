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
        $totalAmount = 0;

        foreach ($items as $index => $item) {
            $validator = Validator::make($item, [
                'product_name' => 'required|string',
                'amount' => 'required|integer|min:1',
                'quantity' => 'integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => "Invalid input for item #$index",
                    'messages' => $validator->errors()
                ], 422);
            }

            $quantity = $item['quantity'] ?? 1;
            $amount = $item['amount'];

            $line_items[] = [
                'price_data' => [
                    'currency' => 'bdt',  // Changed to Bangladeshi Taka
                    'unit_amount' => $amount,
                    'product_data' => [
                        'name' => $item['product_name'],
                    ],
                ],
                'quantity' => $quantity
            ];

            $totalAmount += $amount * $quantity;
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

            // Save deposit in DB
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'amount' => $totalAmount / 100, // convert paisa to BDT
                'session_id' => $session->id,
                'status' => 'completed',
            ]);

            // Save order items
            foreach ($items as $item) {
                OrderItem::create([
                    'deposit_id' => $deposit->id,
                    'product_name' => $item['product_name'],
                    'unit_price' => $item['amount'] / 100, // convert paisa to BDT
                    'quantity' => $item['quantity'] ?? 1,
                ]);
            }
          
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
