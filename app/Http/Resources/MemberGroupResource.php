<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MemberGroupResource extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(fn ($group) => [
                'id'         => $group->id,
                'name'       => $group->name,
                'slug'       => $group->slug,
                'sort_order' => $group->sort_order,
                'members'    => MemberResource::collection($group->activeMembers),
            ]),
        ];
    }
}
