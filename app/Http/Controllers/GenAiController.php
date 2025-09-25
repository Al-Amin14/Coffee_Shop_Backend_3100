<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GenAiController extends Controller
{
    public function generate(Request $request)
    {
        $question = $request->input('question');

        // Fixed 5 questions
        $allowedQuestions = [
            "Which product should I choose? give me short answer",
            "Give me a description about the best coffee. give me short answer",
            "Recommend me one coffee like this. give me short answer",
            "What is the most popular coffee? give me short answer",
            "Which coffee is best for morning? give me short answer"
        ];

        if (!in_array($question, $allowedQuestions)) {
            return response()->json([
                'reply' => "I can only answer the 5 fixed questions."
            ], 200);
        }

        try {
            // Fetch available products from DB
            $products = DB::table('products')
                ->where('is_available', 1)
                ->get(['product_name', 'description', 'price'])
                ->toArray();

            $productContent = "Available products:\n";
            foreach ($products as $p) {
                $productContent .= "- {$p->product_name}: {$p->description}. Price: \${$p->price}\n";
            }

            // Fetch recent orders from DB
            $orders = DB::table('orders')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['product_name', 'quantity', 'total_price', 'status'])
                ->toArray();

            if (!empty($orders)) {
                $productContent .= "\nRecent orders:\n";
                foreach ($orders as $o) {
                    $productContent .= "- {$o->product_name}, Quantity: {$o->quantity}, Total: \${$o->total_price}, Status: {$o->status}\n";
                }
            }

            // Gemini API call with valid roles
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('AI_KEY'),
                    [
                        'contents' => [
                            [
                                'role' => 'model', // database context
                                'parts' => [['text' => $productContent]]
                            ],
                            [
                                'role' => 'user', // user question
                                'parts' => [['text' => $question]]
                            ]
                        ]
                    ]
                );

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response from AI';
                return response()->json(['reply' => $text], 200);
            }

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
