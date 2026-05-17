<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('stocks')->get();

        $categories->transform(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'products_count' => $category->stocks_count,
            ];
        });

        return response()->json($categories);
    }
}

