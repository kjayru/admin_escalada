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
    private static function picsum(string $seed, int $w, int $h): string
    {
        return "https://picsum.photos/seed/{$seed}/{$w}/{$h}";
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($product) {
                return [
                    'id'       => $product->id,
                    'slug'     => $product->slug,
                    'name'     => $product->name,
                    'summary'  => $product->summary,
                    'price'    => $product->price,
                    'currency' => $product->currency,
                    'status'   => $product->status,
                    'category' => [
                        'id'   => $product->category?->id,
                        'name' => $product->category?->name,
                        'slug' => $product->category?->slug,
                    ],
                    'featured_media' => [
                        'url'       => $product->featuredMedia?->url ?? self::picsum($product->slug, 800, 600),
                        'alt'       => $product->featuredMedia?->alt ?? $product->name,
                        'thumbnail' => $product->featuredMedia?->url ?? self::picsum($product->slug, 400, 300),
                    ],
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
