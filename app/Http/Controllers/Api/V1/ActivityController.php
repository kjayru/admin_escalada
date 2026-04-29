<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of published activities.
     */
    public function index(Request $request)
    {
        $query = Activity::published()
            ->orderBy('year', 'desc')
            ->orderBy('order', 'asc');

        if ($request->has('year')) {
            $query->byYear((int) $request->input('year'));
        }

        // Retornar todas las actividades sin paginación para facilitar agrupación por año en frontend
        $activities = $query->get();

        return ActivityResource::collection($activities);
    }

    /**
     * Display the specified activity.
     */
    public function show(int $id)
    {
        $activity = Activity::where('id', $id)
            ->published()
            ->firstOrFail();

        return new ActivityResource($activity);
    }
}
