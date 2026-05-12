<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    private function mediaItem($fileRelation, $legacyRelation, ?string $fallback = null): array
    {
        // Prefer Slimani/Spatie file (saved by MediaPicker)
        if ($fileRelation && !($fileRelation instanceof \Illuminate\Http\Resources\MissingValue)) {
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
        if ($legacyRelation && !($legacyRelation instanceof \Illuminate\Http\Resources\MissingValue)) {
            return [
                'id'  => $legacyRelation->id,
                'url' => $legacyRelation->url ?? '',
                'alt' => $legacyRelation->alt ?? '',
            ];
        }
        // Ultimate fallback
        return [
            'id'  => null,
            'url' => $fallback ?? '',
            'alt' => '',
        ];
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'summary' => $this->summary,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'status' => $this->status,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null,
            'publisher' => $this->publisher ? [
                'id' => $this->publisher->id,
                'name' => $this->publisher->name,
            ] : null,
            'featured_media' => $this->mediaItem(
                $this->whenLoaded('featuredFile', fn() => $this->featuredFile),
                $this->whenLoaded('featuredMedia', fn() => $this->featuredMedia),
                null
            ),
            'gallery' => MediaResource::collection($this->whenLoaded('media')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
