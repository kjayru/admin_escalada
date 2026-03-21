<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberGroupResource;
use App\Models\MemberGroup;

class MemberGroupController extends Controller
{
    public function index(): MemberGroupResource
    {
        $groups = MemberGroup::with(['activeMembers.featuredMedia'])
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        return new MemberGroupResource($groups);
    }
}
