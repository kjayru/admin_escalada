<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($product) {
                return [
                    'id' => $product->id,
                    'slug' => $product->slug,
                    'name' => $product->name,
                    'description' => $product->description ? substr($product->description, 0, 150) . '...' : null,
                    'price' => $product->price,
                    'currency' => $product->currency,
                    'condition' => $product->condition,
                    'location' => $product->location,
                    'category' => [
                        'id' => $product->category?->id,
                        'name' => $product->category?->name,
                        'slug' => $product->category?->slug,
                    ],
                    'featured_media' => $product->featuredMedia ? [
                        'id' => $product->featuredMedia->id,
                        'url' => $product->featuredMedia->url,
                        'thumbnail' => $product->featuredMedia->url,
                    ] : null,
                    'created_at' => $product->created_at?->toIso8601String(),
                ];
            }),
            'meta' => [
                'current_page' => $this->currentPage(),
                'from' => $this->firstItem(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
            ],
        ];
    }
}
