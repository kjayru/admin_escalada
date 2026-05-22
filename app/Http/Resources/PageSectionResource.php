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
        // Helper para convertir URLs de storage para acceso desde el frontend
        $proxyUrl = function ($file) use ($request) {
            if (!$file) {
                return null;
            }
            
            // Si es un MediaFile de Slimani, obtener el primer media de Spatie
            if ($file instanceof \Slimani\MediaManager\Models\File) {
                $spatieMedia = $file->getFirstMedia();
                if (!$spatieMedia) {
                    return null;
                }
                $url = $spatieMedia->getUrl();
            } else {
                // Es un Spatie Media directamente
                $url = $file->getUrl();
            }
            
            // En desarrollo, reemplazar la URL del API por la URL del frontend
            // para que los archivos se sirvan a través del proxy de Nuxt
            if (app()->environment('local', 'development')) {
                // Si el request viene con un header X-Frontend-Base, usar esa URL
                $frontendBase = $request->header('X-Frontend-Base');
                
                if ($frontendBase && str_contains($url, '/storage/')) {
                    // Extraer la parte después de /storage/
                    $storagePath = substr($url, strpos($url, '/storage/') + 9);
                    $url = rtrim($frontendBase, '/') . '/api-proxy/storage/' . $storagePath;
                }
            }
            
            return $url;
        };

        return [
            'id' => $this->id,
            'type' => $this->type,
            'heading' => $this->heading,
            'subheading' => $this->subheading,
            'body' => $this->body,
            'sort_order' => $this->sort_order,
            'settings' => $this->normalizeSettings($this->settings),
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
            'video_file' => $this->videoFile
                ? [
                    'id'        => $this->videoFile->id,
                    'url'       => $proxyUrl($this->videoFile),
                    'file_name' => $this->videoFile->name,
                    'mime_type' => $this->videoFile->mime_type,
                    'size'      => $this->videoFile->size,
                ]
                : null,
            'video_poster' => $this->videoPoster
                ? [
                    'id'        => $this->videoPoster->id,
                    'url'       => $proxyUrl($this->videoPoster),
                    'file_name' => $this->videoPoster->name,
                    'alt'       => $this->videoPoster->alt_text,
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
                        'featured_media' => $item->relationLoaded('featuredFile') && $item->featuredFile
                            ? [
                                'id'        => $item->featuredFile->id,
                                'url'       => $item->featuredFile->getUrl(),
                                'file_name' => $item->featuredFile->name,
                                'alt'       => $item->featuredFile->alt_text,
                            ]
                            : null,
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

    /**
     * Normaliza el campo settings independientemente del formato en que Filament lo guardó.
     *
     * Filament's KeyValue puede persistir en dos formatos:
     *   - Plano:  {"fecha":"Junio 2022"}
     *   - Pairs:  [{"key":"fecha","value":"Junio 2022"}]
     *
     * Ambos se convierten al formato plano para que el frontend siempre pueda
     * acceder a los valores como settings.fecha, settings.background, etc.
     */
    private function normalizeSettings(mixed $settings): array|null
    {
        if (!is_array($settings)) {
            return $settings;
        }

        // Pairs format: array secuencial donde cada elemento tiene {key, value}
        if (isset($settings[0]) && is_array($settings[0]) && array_key_exists('key', $settings[0])) {
            $settings = collect($settings)->pluck('value', 'key')->toArray();
        }

        // Trim keys: previene typos con espacios ("fecha " → "fecha")
        $trimmed = [];
        foreach ($settings as $key => $value) {
            $trimmed[trim((string) $key)] = $value;
        }

        return $trimmed;
    }
}
