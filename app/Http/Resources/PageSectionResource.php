<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageSectionResource extends JsonResource
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
            'type' => $this->type,
            'heading' => $this->heading,
            'subheading' => $this->subheading,
            'body' => $this->body,
            'sort_order' => $this->sort_order,
            'settings' => $this->settings,
            'featured_media' => $this->featuredFile
                ? [
                    'id'        => $this->featuredFile->id,
                    'url'       => $this->featuredFile->getUrl(),
                    'file_name' => $this->featuredFile->name,
                    'alt'       => $this->featuredFile->alt_text,
                ]
                : ($this->featuredMedia ? [
                    'id'        => $this->featuredMedia->id,
                    'url'       => $this->featuredMedia->url,
                    'file_name' => $this->featuredMedia->file_name,
                    'alt'       => $this->featuredMedia->alt,
                ] : null),
            'mobile_image' => $this->mobileImage
                ? [
                    'id'        => $this->mobileImage->id,
                    'url'       => $this->mobileImage->getUrl(),
                    'file_name' => $this->mobileImage->name,
                    'alt'       => $this->mobileImage->alt_text,
                ]
                : null,
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    $settings = $item->settings;
                    // Handle double-encoded JSON (stored as string instead of array)
                    if (is_string($settings)) {
                        $decoded = json_decode($settings, true);
                        $settings = is_array($decoded) ? $decoded : json_decode($decoded, true);
                    }
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'body' => $item->body,
                        'link_url' => $item->link_url,
                        'link_label' => $item->link_label,
                        'sort_order' => $item->sort_order,
                        'settings' => $settings,
                    ];
                });
            }),
            'media' => $this->when(
                $this->relationLoaded('galleryFiles') || $this->relationLoaded('media'),
                function () {
                    if ($this->relationLoaded('galleryFiles') && $this->galleryFiles->isNotEmpty()) {
                        return $this->galleryFiles->map(function ($file) {
                            return [
                                'id' => $file->id,
                                'url' => $file->getUrl(),
                                'file_name' => $file->name,
                                'mime_type' => $file->mime_type,
                                'size' => $file->size,
                                'width' => $file->width,
                                'height' => $file->height,
                                'alt' => $file->alt_text,
                                'title' => $file->name,
                            ];
                        })->values();
                    }

                    return $this->media->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'url' => $media->url,
                            'file_name' => $media->file_name,
                            'mime_type' => $media->mime_type,
                            'size' => $media->size,
                            'width' => $media->width,
                            'height' => $media->height,
                            'alt' => $media->alt,
                            'title' => $media->title,
                        ];
                    })->values();
                }
            ),
        ];
    }
}
