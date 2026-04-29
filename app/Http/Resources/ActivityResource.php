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
            'file' => $this->pdf_url ? [
                'url' => $this->pdf_url,
                'file_name' => basename($this->pdf_path),
            ] : null,
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
