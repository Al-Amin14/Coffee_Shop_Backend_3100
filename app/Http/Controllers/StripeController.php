<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Deposit;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    /**
     * Step 1: Create a Stripe Checkout session
     */
    public function createCheckoutSession(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $items = $request->input('items', []);

        if (empty($items)) {
            return response()->json(['error' => 'No items provided'], 422);
        }

        $line_items = [];
        $totalAmountBDT = 0;

        foreach ($items as $item) {
            $quantity = $item['quantity'] ?? 1;
            $amountBDT = (float) $item['amount'];
            $amountPaisa = (int) round($amountBDT * 100);

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
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Create Stripe checkout session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'mode' => 'payment',
                // CRITICAL FIX: The {CHECKOUT_SESSION_ID} allows React to fetch data later
                'success_url' => 'http://localhost:5173/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://localhost:5173/payment',
            ]);

            // Save initial deposit as 'pending'
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'amount' => $totalAmountBDT,
                'session_id' => $session->id,
                'status' => 'pending', 
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'deposit_id' => $deposit->id,
                    'product_name' => $item['product_name'],
                    'unit_price' => (float) $item['amount'],
                    'quantity' => $item['quantity'] ?? 1,
                ]);
            }

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

    /**
     * Step 2: Fetch data for the Success Page Slip
     */
    public function getCheckoutSuccess($sessionId)
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Retrieve the session from Stripe and expand line items
            $session = Session::retrieve([
                'id' => $sessionId,
                'expand' => ['line_items']
            ]);

            // Update Database Status
            $deposit = Deposit::where('session_id', $sessionId)->first();
            if ($deposit) {
                $deposit->update(['status' => 'completed']);
                
                // Clear user's cart ONLY after successful verification
                Cart::where('user_id', $deposit->user_id)->delete();
            }

            return response()->json([
                'success' => true,
                'order' => [
                    'customer' => $session->customer_details->name ?? 'Valued Customer',
                    'total_amount' => $session->amount_total / 100,
                    'order_number' => strtoupper(substr($session->id, -10)),
                    'items' => array_map(function ($item) {
                        return [
                            'product_name' => $item->description,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->amount_total / ($item->quantity * 100),
                        ];
                    }, $session->line_items->data)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }
}