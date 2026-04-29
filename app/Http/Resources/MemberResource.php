<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'role'          => $this->role,
            'bio'           => $this->bio,
            'featured_home' => (bool) $this->featured_home,
            'photo' => $this->featuredMedia ? [
                'id'        => $this->featuredMedia->id,
                'url'       => $this->featuredMedia->getUrl(),
                'file_name' => $this->featuredMedia->file_name,
                'alt'       => $this->featuredMedia->name ?? '',
            ] : null,
            'sort_order' => $this->sort_order,
        ];
    }
}
