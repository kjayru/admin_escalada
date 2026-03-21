<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'role'  => $this->role,
            'photo' => $this->featuredMedia ? [
                'id'        => $this->featuredMedia->id,
                'url'       => $this->featuredMedia->url,
                'file_name' => $this->featuredMedia->file_name,
                'alt'       => $this->featuredMedia->alt,
            ] : null,
            'sort_order' => $this->sort_order,
        ];
    }
}
