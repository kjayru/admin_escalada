<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupportCampaignResource;
use App\Models\SupportCampaign;
use Illuminate\Http\Request;

class SupportCampaignController extends Controller
{
    /**
     * Display a listing of active support campaigns.
     */
    public function index(Request $request)
    {
        $campaigns = SupportCampaign::where('is_active', true)
            ->with(['featuredMedia', 'supportMethods'])
            ->orderBy('display_order', 'asc')
            ->get();

        return SupportCampaignResource::collection($campaigns);
    }

    /**
     * Display the specified support campaign.
     */
    public function show(string $slug)
    {
        $campaign = SupportCampaign::where('slug', $slug)
            ->where('is_active', true)
            ->with(['featuredMedia', 'supportMethods'])
            ->firstOrFail();

        return new SupportCampaignResource($campaign);
    }
}
