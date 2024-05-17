<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;


class CategoryController extends Controller
{
    //
     public function newCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }

    
    public function getALLCategories()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories], 200);
    }




}
