<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SponsorResource;
use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    /**
     * Display a listing of active sponsors.
     */
    public function index(Request $request)
    {
        $query = Sponsor::where('is_active', true)
            ->with(['logo'])
            ->orderBy('display_order', 'asc');

        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        $sponsors = $query->get();

        return SponsorResource::collection($sponsors);
    }

    /**
     * Display the specified sponsor.
     */
    public function show(string $slug)
    {
        $sponsor = Sponsor::where('slug', $slug)
            ->where('is_active', true)
            ->with(['logo'])
            ->firstOrFail();

        return new SponsorResource($sponsor);
    }
}
