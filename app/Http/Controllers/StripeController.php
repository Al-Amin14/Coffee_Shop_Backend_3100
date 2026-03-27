<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Deposit;
use App\Models\OrderItem;
use App\Models\Cart;

class StripeController extends Controller
{
    /**
     * Initialize Stripe
     */
    private function initStripe()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Validate incoming request
     */
    private function validateItems($items)
    {
        return Validator::make(
            ['items' => $items],
            [
                'items' => 'required|array|min:1',
                'items.*.product_name' => 'required|string|max:255',
                'items.*.amount' => 'required|numeric|min:1',
                'items.*.quantity' => 'nullable|integer|min:1'
            ]
        );
    }

    /**
     * Convert BDT to Paisa safely
     */
    private function convertToPaisa($amount)
    {
        return (int) round($amount * 100);
    }

    /**
     * Step 1: Create Stripe Checkout Session
     */
    public function createCheckoutSession(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $items = $request->input('items');

        // Validate input
        $validator = $this->validateItems($items);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        $lineItems = [];
        $totalAmountBDT = 0;

        foreach ($items as $item) {
            $quantity = $item['quantity'] ?? 1;
            $amountBDT = (float) $item['amount'];
            $amountPaisa = $this->convertToPaisa($amountBDT);

            $lineItems[] = [
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
            $this->initStripe();

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => config('app.frontend_url') . '/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('app.frontend_url') . '/payment',
            ]);

            // Store deposit (pending)
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'amount' => $totalAmountBDT,
                'session_id' => $session->id,
                'status' => 'pending',
            ]);

            // Save order items
            foreach ($items as $item) {
                OrderItem::create([
                    'deposit_id' => $deposit->id,
                    'product_name' => $item['product_name'],
                    'unit_price' => (float) $item['amount'],
                    'quantity' => $item['quantity'] ?? 1,
                ]);
            }

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'checkout_url' => $session->url
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe Session Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Stripe error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Step 2: Handle Success Page Data
     */
    public function getCheckoutSuccess($sessionId)
    {
        try {
            $this->initStripe();

            $session = Session::retrieve([
                'id' => $sessionId,
                'expand' => ['line_items', 'customer_details']
            ]);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session'
                ], 404);
            }

            // Verify payment status
            if ($session->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not completed'
                ], 400);
            }

            // Update deposit
            $deposit = Deposit::where('session_id', $sessionId)->first();

            if ($deposit && $deposit->status !== 'completed') {
                $deposit->update(['status' => 'completed']);

                // Clear cart
                Cart::where('user_id', $deposit->user_id)->delete();
            }

            // Format items
            $items = [];
            foreach ($session->line_items->data as $item) {
                $items[] = [
                    'product_name' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => ($item->amount_total / 100) / $item->quantity,
                    'total_price' => $item->amount_total / 100
                ];
            }

            return response()->json([
                'success' => true,
                'order' => [
                    'customer' => $session->customer_details->name ?? 'Customer',
                    'email' => $session->customer_details->email ?? null,
                    'total_amount' => $session->amount_total / 100,
                    'currency' => strtoupper($session->currency),
                    'payment_status' => $session->payment_status,
                    'order_number' => strtoupper(substr($session->id, -10)),
                    'items' => $items
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe Verification Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optional: Webhook (recommended for production)
     */
    public function handleWebhook(Request $request)
    {
        Log::info('Stripe Webhook Received', $request->all());

        // You can verify signature here later
        // and update deposit status safely from Stripe events

        return response()->json(['status' => 'received']);
    }
}
