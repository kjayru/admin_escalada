<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SponsorPlacementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'placement'  => $this->placement,
            'title'      => $this->title,
            'body'       => $this->body,
            'banner'     => [
                'id'  => $this->bannerMedia?->id,
                'url' => $this->bannerMedia?->url ?? null,
                'alt' => $this->bannerMedia?->alt ?? $this->title,
            ],
            'link_url'   => $this->link_url,
            'sort_order' => $this->sort_order,
            'sponsor'    => [
                'id'   => $this->sponsor?->id,
                'name' => $this->sponsor?->name,
                'slug' => $this->sponsor?->slug,
                'logo' => [
                    'id'  => $this->sponsor?->logo?->id,
                    'url' => $this->sponsor?->logo?->url ?? null,
                    'alt' => $this->sponsor?->logo?->alt ?? $this->sponsor?->name,
                ],
            ],
        ];
    }
}
