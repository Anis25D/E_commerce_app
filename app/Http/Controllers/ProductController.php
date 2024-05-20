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


        $pictureKeys = ['picture0', 'picture1', 'picture2', 'picture3'];
        $picturePaths = [];

        foreach ($pictureKeys as $key) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('products', 'public');
                $picturePaths[] = $path;

                Picture::create([
                    'product_id' => $productId,
                    'picture' => $path,
                ]);
            }
        }

        

        DB::commit();

        return response()->json([
            'pictures'=>$picturePaths,
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
    // Retrieve products with their associated pictures' paths
    $products = Product::with('pictures')->get()->map(function ($product) {
        // Extract the picture paths from the product's pictures relationship
        $picturePaths = $product->pictures->pluck('picture')->toArray();
        // Merge the picture paths with all product attributes
        return array_merge($product->toArray(), ['pictures' => $picturePaths]);
    });

    return response()->json([
        'products' => $products,
    ], 200);
}


public function get_products_by_category($category_id)
{
    $products = Product::where('category_id', $category_id)
                        ->with('pictures')
                        ->get()
                        ->map(function ($product) {
                            // Extract the picture paths from the product's pictures relationship
                            $picturePaths = $product->pictures->pluck('picture')->toArray();
                            // Merge the picture paths with all product attributes
                            return array_merge($product->toArray(), ['pictures' => $picturePaths]);
                        });

    return response()->json([
        'products' => $products,
    ], 200);
}



// public function get_all_products()
// {
//     // Eager load the 'pictures' relationship with the products
//     $products = Product::with('pictures')->get();

//     return response()->json([
//         'products' => $products,
//     ], 200);
// }
    

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

    public function delete_product($id)
{
    DB::beginTransaction();

    try {
        // Find the product by ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // // Get the picture paths
        // $picturePaths = $product->pictures->pluck('picture');

        // // Delete associated pictures from storage
        // foreach ($picturePaths as $path) {
        //     Storage::disk('public')->delete($path);
        // }

        $product->pictures()->delete();

        $product->options()->delete();

        $product->delete();

        DB::commit();

        return response()->json(['message' => 'Product deleted successfully.'], 200);
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['message' => 'Failed to delete product.'], 500);
    }
}

public function count_products()
{
    try {
       
        $count = Product::count();

        return response()->json(['count' => $count], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to count products.'], 500);
    }
}

public function get_product($id)
{
    try {
        // Retrieve the product with associated pictures and options
        $product = Product::with(['pictures', 'options'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // Extract the picture paths from the product's pictures relationship
        $picturePaths = $product->pictures->pluck('picture')->toArray();

        // Merge the picture paths with all product attributes
        $productData = array_merge($product->toArray(), ['pictures' => $picturePaths]);

        return response()->json(['product' => $productData], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to retrieve product.'], 500);
    }
}


}
