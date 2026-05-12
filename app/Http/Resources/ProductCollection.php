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
    private function mediaItem($fileRelation, $legacyRelation): array
    {
        // Prefer Slimani/Spatie file (saved by MediaPicker)
        if ($fileRelation) {
            $spatieMedia = $fileRelation->getFirstMedia();
            $url = $spatieMedia?->getUrl();
            if ($url) {
                return [
                    'id'  => $fileRelation->id,
                    'url' => $url,
                    'alt' => $fileRelation->alt_text ?? '',
                ];
            }
        }
        // Fallback to legacy media
        if ($legacyRelation) {
            return [
                'id'  => $legacyRelation->id,
                'url' => $legacyRelation->url ?? '',
                'alt' => $legacyRelation->alt ?? '',
            ];
        }
        // No media
        return [
            'id'  => null,
            'url' => null,
            'alt' => '',
        ];
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($product) {
                $featuredMedia = $this->mediaItem(
                    $product->featuredFile ?? null,
                    $product->featuredMedia ?? null
                );

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
                    'featured_media' => $featuredMedia,
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
