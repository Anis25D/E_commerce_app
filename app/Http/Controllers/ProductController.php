<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Option;
use App\Models\Picture;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;



class ProductController extends Controller
{
    //
    public function create_product(Request $request)
{

    // $seller_id = $request->user()->seller->id;

    DB::beginTransaction();

    try{

        $product = Product::create([
            'seller_id' => $request->seller_id,
            'name' => $request->name,
            'brand' => $request->brand,
            'price' => $request->price,
            'onsale_price' => $request->onsale_price,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'quantity' => $request->quantity,
        ]);
    
        $productId = $product->id;
    
        if ($request->colors){
            $colors = $request->colors;
    
            foreach ($colors as $color) {
                Option::create([
                    'product_id'=> $productId,
                    'type'=> 'color',
                    'value'=> $color
                ]);
            }  
        }
    
        if ($request->sizes){
            $sizes = $request->sizes;
    
            foreach ($sizes as $size) {
                Option::create([
                    'product_id'=> $productId,
                    'type'=> 'size',
                    'value'=> $size
                ]);
            }  
        }

        if ($request->storages){
            $storages = $request->storages;
    
            foreach ($storages as $storage) {
                Option::create([
                    'product_id'=> $productId,
                    'type'=> 'storage',
                    'value'=> $storage
                ]);
            }  
        }

        $picturePaths = [];
            if ($request->hasFile('pictures')) {
                foreach ($request->file('pictures') as $picture) {
                    $path = $picture->store('products', 'public'); 
                    // $picturePaths[] = str_replace('public/', 'storage/', $path); // Store path for later retrieval
                    $picturePaths[] = $path;
                }
            }


            foreach ($picturePaths as $path) {
                Picture::create([
                    'product_id' => $productId,
                    'picture' => $path
                ]);
            }

        DB::commit();

        return response()->json([
            'product' => $product,
            'message' => 'Product created successfully.',
        ], 201);


    }catch(\Exception $e){
        DB::rollback();
        return response()->json(['message' => $e], 500);
    }
    
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
