<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id= auth()->user()->id;
        $products = Product::where('user_id', $user_id)->get()->map(function ($product) {
            $product->banner_image = $product->banner_image ? asset("storage/" . $product->banner_image) : null;
            return $product;
        });
        return response()->json([
            'status' => true,
            'message' => 'Products retrieved successfully',
            'products' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'required|numeric',
            'banner_image' => 'nullable|file|image|max:2048'
        ]);
    
        $data = [
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'cost' => $request->cost,
        ];
    
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('products', 'public');
        }
    
        $product = Product::create($data);
    
        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'status' => true,
            'message' => 'Product retrieved successfully',
            'product' => $product
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);
    $data['description'] = isset($request->description) ? $request->description : $product->description;
    $data['cost'] = isset($request->cost) ? $request->cost : $product->cost;

    $data = [
        'title' => $request->title,
    ];

    if ($request->hasFile('banner_image')) {
        if ($product->banner_image) {
            // Delete old image
            Storage::disk('public')->delete($product->banner_image);
        }
        $data['banner_image'] = $request->file('banner_image')->store('products', 'public');
    }

    $product->update($data);

    return response()->json([
        'status' => true,
        'message' => 'Product updated successfully',
        'product' => $product
    ]);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}
