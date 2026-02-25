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
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'body' => $item->body,
                        'link_url' => $item->link_url,
                        'link_label' => $item->link_label,
                        'sort_order' => $item->sort_order,
                        'settings' => $item->settings,
                    ];
                });
            }),
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
