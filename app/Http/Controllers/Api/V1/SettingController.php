<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Get all public site settings.
     */
    public function index()
    {
        $settings = SiteSetting::all()->pluck('value', 'key');

        return response()->json([
            'settings' => $settings
        ]);
    }

    /**
     * Get a specific site setting by key.
     */
    public function show(string $key)
    {
        $value = SiteSetting::get($key);

        if ($value === null) {
            return response()->json([
                'message' => 'Configuración no encontrada'
            ], 404);
        }

        return response()->json([
            'key' => $key,
            'value' => $value
        ]);
    }
}
