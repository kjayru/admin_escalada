<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Slimani\MediaManager\Models\File as SlimaniFile;

class Gym extends Model
{
    protected $fillable = [
        'name',
        'address',
        'website_url',
        'logo_media_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function logo(): BelongsTo
    {
        return $this->belongsTo(SlimaniFile::class, 'logo_media_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
