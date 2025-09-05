<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            Log::error("Webhook Error: Invalid Signature", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\UnexpectedValueException $e) {
            Log::error("Webhook Error: Invalid Payload", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                $deposit = Deposit::where('session_id', $session->id)->first();

                if ($deposit) {
                    if ($deposit->status !== 'Completed') {
                        $deposit->status = 'Completed';
                        $deposit->save();

                        Log::info('Deposit updated to Completed.', [
                            'session_id' => $session->id,
                            'amount' => $deposit->amount,
                            'user_id' => $deposit->user_id,
                        ]);

                        // ✅ Create orders from OrderItems
                        $items = OrderItem::where('deposit_id', $deposit->id)->get();

                        foreach ($items as $item) {
                            Order::create([
                                'user_id' => $deposit->user_id,
                                'product_name' => $item->product_name,
                                'quantity' => $item->quantity,
                                'total_price' => $item->unit_price * $item->quantity,
                                'status' => 'processing',
                                'image_path' => null // optionally load from product table
                            ]);
                        }

                        Log::info('Orders created for user after checkout.', [
                            'user_id' => $deposit->user_id,
                            'order_count' => count($items)
                        ]);
                    }
                } else {
                    Log::warning("No deposit found for session_id: " . $session->id);
                }

                break;

            default:
                Log::info("Unhandled event type: " . $event->type);
                break;
        }

        return response()->json(['status' => 'Webhook received']);
    }
}
