<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;




class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Fetch all orders for authenticated user
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function getAllOrders()
    {
        $orders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    // Store new order
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
        ]);

        $product = Product::where('product_name', $validated['product_name'])->first();

        $order = new Order();
        $order->user_id = $user->id;
        $order->product_name = $validated['product_name'];
        $order->quantity = $validated['quantity'];
        $order->total_price = $validated['total_price'];
        $order->status = $validated['status'];
        $order->MangerConfirm = 'pending';
        $order->image_path = $product->image_path ?? null;
        $order->save();

        return response()->json(['message' => 'Order placed', 'order' => $order], 201);
    }

    // Delete order
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        if (auth()->id() !== $order->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted']);
    }

    public function updateConfirmedBy(Request $request, $id)
    {
        $request->validate([
            'confirmed_by' => 'required|string|max:255',
        ]);

        $order = Order::find(id: $id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found!',
            ], 404);
        }

        $order->update([
            'confirmed_by' => $request->confirmed_by,
            'MangerConfirm' => 'completed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Confirmed by updated successfully!',
            'order' => $order,
        ]);
    }

}
