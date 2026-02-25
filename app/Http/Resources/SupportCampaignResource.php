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
            'title' => $this->title,
            'description' => $this->description,
            'goal_amount' => $this->goal_amount,
            'currency' => $this->currency,
            'start_date' => $this->start_date?->toIso8601String(),
            'end_date' => $this->end_date?->toIso8601String(),
            'featured_media' => $this->featuredMedia ? [
                'id' => $this->featuredMedia->id,
                'url' => $this->featuredMedia->url,
                'alt_text' => $this->featuredMedia->alt_text,
            ] : null,
            'support_methods' => $this->supportMethods->map(function ($method) {
                return [
                    'id' => $method->id,
                    'type' => $method->type,
                    'name' => $method->name,
                    'instructions' => $method->instructions,
                    'is_active' => $method->is_active,
                ];
            }),
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
