<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransparencyDocumentResource extends JsonResource
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
            'type' => $this->type,
            'year' => $this->year,
            'file' => $this->media ? [
                'id' => $this->media->id,
                'url' => $this->media->getUrl(),
                'file_name' => $this->media->name,
                'mime_type' => $this->media->mime_type,
                'size' => $this->media->size,
            ] : null,
            'image' => $this->imageMedia ? [
                'id' => $this->imageMedia->id,
                'url' => $this->imageMedia->getUrl(),
                'file_name' => $this->imageMedia->name,
                'mime_type' => $this->imageMedia->mime_type,
                'size' => $this->imageMedia->size,
            ] : null,
            'published_at' => $this->published_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
