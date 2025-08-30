<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'stock_quantity' => 'required|integer',
            'unit' => 'nullable|string|max:20',
            'is_available' => 'required|boolean',
            'image_path' => 'nullable|string',
        ]);

        // Create product
        $product = Product::create($request->all());

        return response()->json([
            'success' => true,
            'product' => $product
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
