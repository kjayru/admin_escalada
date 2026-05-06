<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TimelineResource;
use App\Models\Timeline;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    /**
     * Display a listing of published timeline milestones.
     */
    public function index(Request $request)
    {
        $query = Timeline::published()
            ->with(['featuredFile', 'media'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('order', 'asc');

        if ($request->has('year')) {
            $query->byYear((int) $request->input('year'));
        }

        $timelines = $query->get();

        return TimelineResource::collection($timelines);
    }

    /**
     * Display the specified timeline milestone.
     */
    public function show(int $id)
    {
        $timeline = Timeline::where('id', $id)
            ->published()
            ->with(['featuredFile', 'media'])
            ->firstOrFail();

        return new TimelineResource($timeline);
    }
}
