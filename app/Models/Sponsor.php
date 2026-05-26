<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Slimani\MediaManager\Models\File as MediaFile;

class Sponsor extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'tagline',
        'website_url',
        'buy_url',
        'logo_media_id',
        'circle_logo_media_id',
        'section_logo_media_id',
        'slide_image_media_id',
        'gallery_1_media_id',
        'gallery_2_media_id',
        'gallery_3_media_id',
        'gallery_4_media_id',
        'highlight_media_id',
        'contact_name',
        'contact_title',
        'contact_text',
        'contact_media_id',
        'facebook_url',
        'twitter_url',
        'email',
        'status',
        'og_title',
        'og_description',
        'og_image_media_id',
        'og_image_width',
        'og_image_height',
    ];

    public function logo(): BelongsTo
    {
        return $this->belongsTo(LegacyMedia::class, 'logo_media_id');
    }

    public function logoFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'logo_media_id');
    }

    public function circleLogoFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'circle_logo_media_id');
    }

    public function sectionLogoFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'section_logo_media_id');
    }

    public function slideImage(): BelongsTo
    {
        return $this->belongsTo(LegacyMedia::class, 'slide_image_media_id');
    }

    public function slideImageFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'slide_image_media_id');
    }

    public function gallery1(): BelongsTo
    {
        return $this->belongsTo(LegacyMedia::class, 'gallery_1_media_id');
    }

    public function gallery1File(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'gallery_1_media_id');
    }

    public function gallery2(): BelongsTo
    {
        return $this->belongsTo(LegacyMedia::class, 'gallery_2_media_id');
    }

    public function gallery2File(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'gallery_2_media_id');
    }

    public function gallery3(): BelongsTo
    {
        return $this->belongsTo(LegacyMedia::class, 'gallery_3_media_id');
    }

    public function gallery3File(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'gallery_3_media_id');
    }

    public function gallery4(): BelongsTo
    {
        return $this->belongsTo(LegacyMedia::class, 'gallery_4_media_id');
    }

    public function gallery4File(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'gallery_4_media_id');
    }

    public function highlightFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'highlight_media_id');
    }

    public function contactMedia(): BelongsTo
    {
        return $this->belongsTo(LegacyMedia::class, 'contact_media_id');
    }

    public function contactMediaFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'contact_media_id');
    }

    public function ogImageFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'og_image_media_id');
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
