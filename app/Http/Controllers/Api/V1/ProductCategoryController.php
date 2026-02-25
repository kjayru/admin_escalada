<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of active product categories.
     */
    public function index()
    {
        $categories = ProductCategory::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'slug', 'description']);

        return response()->json([
            'data' => $categories
        ]);
    }

    /**
     * Display the specified category.
     */
    public function show(string $slug)
    {
        $category = ProductCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'created_at' => $category->created_at?->toIso8601String(),
            ]
        ]);
    }
}
