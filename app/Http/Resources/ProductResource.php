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
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'condition' => $this->condition,
            'status' => $this->status,
            'location' => $this->location,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'publisher' => [
                'id' => $this->publisher?->id,
                'name' => $this->publisher?->name,
                'email' => $this->publisher?->email,
            ],
            'featured_media' => $this->featuredMedia ? [
                'id' => $this->featuredMedia->id,
                'url' => $this->featuredMedia->url,
                'alt_text' => $this->featuredMedia->alt_text,
            ] : null,
            'gallery' => MediaResource::collection($this->whenLoaded('media')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
