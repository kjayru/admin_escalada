<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of published products.
     */
    public function index(Request $request)
    {
        $query = Product::where('status', 'published')
            ->with(['category', 'featuredMedia', 'publisher'])
            ->orderBy('created_at', 'desc');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate($request->input('per_page', 12));

        return new ProductCollection($products);
    }

    /**
     * Display the specified product.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'published')
            ->with(['category', 'featuredMedia', 'publisher', 'media'])
            ->firstOrFail();

        return new ProductResource($product);
    }

    /**
     * Store a new inquiry for a product.
     */
    public function storeInquiry(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $product = Product::findOrFail($id);

        ProductInquiry::create([
            'product_id' => $product->id,
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => 'Consulta enviada al vendedor'
        ], 201);
    }
}
