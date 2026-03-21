<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimelineResource extends JsonResource
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
            'date' => $this->date,
            'title' => $this->title,
            'body' => $this->body,
            'year' => $this->year,
            'month' => $this->month,
            'order' => $this->order,
            'image' => $this->media ? [
                'id' => $this->media->id,
                'url' => $this->media->url,
                'file_name' => $this->media->file_name,
                'alt' => $this->media->alt,
            ] : null,
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
