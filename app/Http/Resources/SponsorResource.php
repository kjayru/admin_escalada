<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SponsorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    private function mediaItem($media, string $fallback = null): array
    {
        return [
            'id'  => $media?->id,
            'url' => $media?->url ?? $fallback,
            'alt' => $media?->alt ?? '',
        ];
    }

    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'slug'        => $this->slug,
            'name'        => $this->name,
            'tagline'     => $this->tagline,
            'description' => $this->description,
            'website_url' => $this->website_url,

            // -- Imágenes --
            'logo' => $this->mediaItem(
                $this->logo,
                "https://picsum.photos/seed/{$this->slug}-logo/400/200"
            ),
            'slide_image' => $this->mediaItem($this->slideImage),
            'gallery' => array_values(array_filter([
                $this->gallery1 ? $this->mediaItem($this->gallery1) : null,
                $this->gallery2 ? $this->mediaItem($this->gallery2) : null,
                $this->gallery3 ? $this->mediaItem($this->gallery3) : null,
                $this->gallery4 ? $this->mediaItem($this->gallery4) : null,
            ])),

            // -- Tarjeta de representante --
            'contact' => [
                'name'  => $this->contact_name,
                'title' => $this->contact_title,
                'text'  => $this->contact_text,
                'image' => $this->mediaItem($this->contactMedia),
            ],

            // -- Redes sociales --
            'social' => [
                'facebook' => $this->facebook_url,
                'twitter'  => $this->twitter_url,
                'email'    => $this->email,
            ],

            'status'     => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
