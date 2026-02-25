<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorPlacement extends Model
{
    protected $fillable = [
        'sponsor_id',
        'placement',
        'title',
        'body',
        'banner_media_id',
        'link_url',
        'start_at',
        'end_at',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_media_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            });
    }

    public function scopeForPlacement($query, string $placement)
    {
        return $query->where('placement', $placement);
    }
}
