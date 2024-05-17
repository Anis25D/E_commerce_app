<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function create_product(Request $request)
{

    $seller_id = $request->user()->seller->id;


    $product = Product::create([
        'name' => $request->name,
        'seller_id' => $request->seller_id,
        'price' => $request->price,
        'onsale_price' => $request->onsale_price,
        'category_id' => $request->category_id,
        'description' => $request->description,
        'quantity' => $request->quantity,
       
    ]);

    return response()->json([
        'product' => $product,
        'message' => 'Product created successfully.',
    ], 201);
}


    public function get_all_products()
    {
        $products = Product::all();

        return response()->json([
            'products' => $products,
        ], 200);
    }
    

    public function update(Request $request ,string $product_id)
    {
        $product = Product::find($product_id);

        $product->update([

            'name' => $request->name,
            'seller_id' => $request->seller_id,
            'price' => $request->price,
            'onsale_price' => $request->onsale_price,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'rating' => $request->rating,
            'quantity' => $request->quantity,

        ]);

        return response()->json([
            'product' => $product,
            'message' => 'product updated'
        ], 200);
    }

}
