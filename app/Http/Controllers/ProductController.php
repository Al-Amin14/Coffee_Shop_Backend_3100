<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{


    // Show single product details
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }

    // Create a new product
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:20',
            'is_available' => 'required|boolean',
            'image_path' => 'nullable|string',
        ]);

        $product = Product::create($request->all());

        return response()->json([
            'success' => true,
            'product' => $product,
        ], 201);
    }

    // Update existing product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $request->validate([
            'product_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|string|max:100',
            'price' => 'sometimes|required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'unit' => 'nullable|string|max:20',
            'is_available' => 'sometimes|required|boolean',
            'image_path' => 'nullable|string',
        ]);

        $product->update($request->all());

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    public function allProducts(Request $request)
    {
        $products = DB::select("SELECT * FROM products");

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }


}
