<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a specific menu by location.
     */
    public function show(string $location)
    {
        $menu = Menu::where('location', $location)
            ->where('is_active', true)
            ->with(['items' => function ($query) {
                $query->whereNull('parent_id')
                      ->orderBy('order')
                      ->with('children');
            }])
            ->firstOrFail();

        return new MenuResource($menu);
    }
}
