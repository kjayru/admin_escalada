<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GymResource;
use App\Models\Gym;

class GymController extends Controller
{
    public function index()
    {
        $gyms = Gym::active()
            ->with('logo')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return GymResource::collection($gyms);
    }
}
