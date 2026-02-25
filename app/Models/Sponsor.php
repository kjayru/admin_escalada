<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sponsor extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'website_url',
        'logo_media_id',
        'status',
    ];

    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    public function placements(): HasMany
    {
        return $this->hasMany(SponsorPlacement::class);
    }

    public function activePlacements(): HasMany
    {
        return $this->hasMany(SponsorPlacement::class)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            })
            ->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
