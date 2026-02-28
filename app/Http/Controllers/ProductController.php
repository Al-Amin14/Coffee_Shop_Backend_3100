<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Get all products.
     */
    public function allProducts()
    {
        // Using scopeAvailable if you want only active products
        $products = Product::all(); 
        return response()->json([
            'success' => true,
            'products' => $products
        ], 200);
    }

    /**
     * Show a single product's details.
     * CRITICAL: Your frontend 'useEffect' calls this on refresh to get 
     * the latest 'stock_quantity' from the database.
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => $product
        ], 200);
    }

    /**
     * Update stock quantity when adding to cart.
     * This physically changes the number in your SQL table.
     */
    public function updateStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid quantity provided.'], 400);
        }

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        // 1. Calculate the new stock
        $newStock = $product->stock_quantity - $request->quantity;

        // 2. CHECK CONSTRAINT PROTECTION (> 1)
        if ($newStock <0) {
            return response()->json([
                'error' => 'Insufficient stock. Database policy requires at least 2 items remain.',
                'current_stock' => $product->stock_quantity
            ], 422);
        }

        // 3. PERSIST TO DATABASE
        $product->stock_quantity = $newStock;
        
        if ($product->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock reduced successfully.',
                'new_stock' => $product->stock_quantity
            ], 200);
        }

        return response()->json(['error' => 'Failed to save stock update.'], 500);
    }

    /**
     * Add product to the Cart (Charts) table.
     */
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'      => 'required|integer',
            'product_id'   => 'required|integer',
            'product_name' => 'required|string',
            'quantity'     => 'required|integer|min:1',
            'unit_price'   => 'required|numeric',
            'total_price'  => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Missing required cart data.'], 400);
        }

        // We use DB::table to ensure direct insertion into the carts table
        $inserted = DB::table('carts')->insert([
            'user_id'      => $request->user_id,
            'product_id'   => $request->product_id,
            'product_name' => $request->product_name,
            'quantity'     => $request->quantity,
            'unit_price'   => $request->unit_price,
            'total_price'  => $request->total_price,
            'image_path'   => $request->image_path,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        if ($inserted) {
            return response()->json(['success' => true], 201);
        }

        return response()->json(['error' => 'Cart insertion failed.'], 500);
    }

    // --- ADMIN METHODS ---

    public function store(Request $request)
    {
        $product = Product::create($request->all());
        return response()->json(['success' => true, 'product' => $product], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['error' => 'Not found'], 404);
        
        $product->update($request->all());
        return response()->json(['success' => true, 'product' => $product]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'Product not found'], 404);
    }
}