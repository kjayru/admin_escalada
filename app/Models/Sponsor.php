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
        'tagline',
        'website_url',
        'logo_media_id',
        'slide_image_media_id',
        'gallery_1_media_id',
        'gallery_2_media_id',
        'gallery_3_media_id',
        'gallery_4_media_id',
        'contact_name',
        'contact_title',
        'contact_text',
        'contact_media_id',
        'facebook_url',
        'twitter_url',
        'email',
        'status',
    ];

    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    public function slideImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'slide_image_media_id');
    }

    public function gallery1(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'gallery_1_media_id');
    }

    public function gallery2(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'gallery_2_media_id');
    }

    public function gallery3(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'gallery_3_media_id');
    }

    public function gallery4(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'gallery_4_media_id');
    }

    public function contactMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'contact_media_id');
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
