<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportCampaignResource extends JsonResource
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
            'status' => $this->status,
            'start_at' => $this->start_at?->toIso8601String(),
            'end_at' => $this->end_at?->toIso8601String(),
            'methods' => $this->whenLoaded('activeMethods', function () {
                return $this->activeMethods->map(function ($method) {
                    // settings puede llegar como string JSON o como array
                    $settings = is_string($method->settings)
                        ? json_decode($method->settings, true)
                        : ($method->settings ?? []);

                    // Imagen: primero media subida, luego settings[image], luego null
                    $imageUrl = $method->media?->getUrl()
                        ?? $settings['image']
                        ?? null;

                    return [
                        'id'       => $method->id,
                        'type'     => $method->type,
                        'title'    => $method->title,
                        'body'     => $method->body,
                        'image'    => $imageUrl,
                        'settings' => $settings,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
