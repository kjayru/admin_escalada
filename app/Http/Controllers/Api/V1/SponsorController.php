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
        $sponsors = Sponsor::active()
            ->with(['logo', 'slideImage', 'gallery1', 'gallery2', 'gallery3', 'gallery4', 'contactMedia', 'logoFile', 'circleLogoFile', 'sectionLogoFile', 'slideImageFile', 'gallery1File', 'gallery2File', 'gallery3File', 'gallery4File', 'highlightFile', 'contactMediaFile', 'ogImageFile'])
            ->orderBy('name', 'asc')
            ->get();

        return SponsorResource::collection($sponsors);
    }

    /**
     * Display the specified sponsor.
     */
    public function show(string $slug)
    {
        $sponsor = Sponsor::where('slug', $slug)
            ->active()
            ->with(['logo', 'slideImage', 'gallery1', 'gallery2', 'gallery3', 'gallery4', 'contactMedia', 'logoFile', 'circleLogoFile', 'sectionLogoFile', 'slideImageFile', 'gallery1File', 'gallery2File', 'gallery3File', 'gallery4File', 'highlightFile', 'contactMediaFile', 'ogImageFile'])
            ->firstOrFail();

        return new SponsorResource($sponsor);
    }
}
