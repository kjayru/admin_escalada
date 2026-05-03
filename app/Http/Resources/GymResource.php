<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GymResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'address'     => $this->address,
            'website_url' => $this->website_url,
            'logo'        => $this->logo ? $this->logo->getUrl() : null,
            'sort_order'  => $this->sort_order,
            'is_active'   => $this->is_active,
        ];
    }
}
