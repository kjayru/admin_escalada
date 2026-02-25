<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
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
            'title' => $this->title,
            'template' => $this->template,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'sections' => PageSectionResource::collection($this->whenLoaded('sections')),
            'media' => MediaResource::collection($this->whenLoaded('media')),
            'published_at' => $this->published_at?->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
