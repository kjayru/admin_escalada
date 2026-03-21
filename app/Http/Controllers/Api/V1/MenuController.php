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
        $menu = Menu::where('name', $location)
            ->with(['activeItems' => function ($query) {
                $query->with(['children' => function ($q) {
                    $q->where('is_active', true)->orderBy('sort_order');
                }]);
            }])
            ->firstOrFail();

        return new MenuResource($menu);
    }
}
