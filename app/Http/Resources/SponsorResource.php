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
                'url' => $legacyRelation->url ?? $fallback,
                'alt' => $legacyRelation->alt ?? '',
            ];
        }
        return ['id' => null, 'url' => $fallback, 'alt' => ''];
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
            'buy_url'     => $this->buy_url,

            // -- Imágenes --
            'logo' => $this->mediaItem(
                $this->whenLoaded('logoFile', fn() => $this->logoFile),
                $this->whenLoaded('logo', fn() => $this->logo),
                "https://picsum.photos/seed/{$this->slug}-logo/400/200"
            ),
            'circle_logo' => $this->mediaItem(
                $this->whenLoaded('circleLogoFile', fn() => $this->circleLogoFile),
                null,
            ),
            'section_logo' => $this->mediaItem(
                $this->whenLoaded('sectionLogoFile', fn() => $this->sectionLogoFile),
                null,
            ),
            'slide_image' => $this->mediaItem(
                $this->whenLoaded('slideImageFile', fn() => $this->slideImageFile),
                $this->whenLoaded('slideImage', fn() => $this->slideImage),
            ),
            'gallery' => array_values(array_filter([
                $this->gallery1File || $this->gallery1 ? $this->mediaItem($this->gallery1File, $this->gallery1) : null,
                $this->gallery2File || $this->gallery2 ? $this->mediaItem($this->gallery2File, $this->gallery2) : null,
                $this->gallery3File || $this->gallery3 ? $this->mediaItem($this->gallery3File, $this->gallery3) : null,
                $this->gallery4File || $this->gallery4 ? $this->mediaItem($this->gallery4File, $this->gallery4) : null,
            ])),
            'highlight_image' => $this->mediaItem(
                $this->whenLoaded('highlightFile', fn() => $this->highlightFile),
                null,
            ),

            // -- Tarjeta de representante --
            'contact' => [
                'name'  => $this->contact_name,
                'title' => $this->contact_title,
                'text'  => $this->contact_text,
                'image' => $this->mediaItem($this->contactMediaFile, $this->contactMedia),
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
