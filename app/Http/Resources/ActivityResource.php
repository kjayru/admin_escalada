<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'name' => $this->name,
            'year' => $this->year,
            'order' => $this->order,
            'file' => $this->media ? [
                'id' => $this->media->id,
                'url' => $this->media->url,
                'file_name' => $this->media->file_name,
                'mime_type' => $this->media->mime_type,
                'size' => $this->media->size,
            ] : null,
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
