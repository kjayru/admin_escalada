<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SponsorPlacementResource;
use App\Models\SponsorPlacement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SponsorPlacementController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = SponsorPlacement::with(['sponsor.logo', 'bannerMedia'])
            ->active()
            ->orderBy('sort_order');

        if ($placement = $request->query('placement')) {
            $query->forPlacement($placement);
        }

        return SponsorPlacementResource::collection($query->get());
    }
}
