<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;

class PageController extends Controller
{
    /**
     * Display a listing of published pages.
     */
    public function index()
    {
        $pages = Page::where('status', 'published')
            ->with(['sections.items'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return PageResource::collection($pages);
    }

    /**
     * Display the specified page by slug.
     */
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->with(['sections.items', 'sections.media', 'media'])
            ->firstOrFail();

        return new PageResource($page);
    }
}
