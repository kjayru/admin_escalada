<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Slimani\MediaManager\Models\File as MediaFile;

class PageSection extends Model
{
    protected $fillable = [
        'page_id',
        'contentable_type',
        'contentable_id',
        'type',
        'sort_order',
        'heading',
        'subheading',
        'body',
        'settings',
        'featured_media_id',
        'status',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function contentable(): MorphTo
    {
        return $this->morphTo();
    }

    /** @deprecated Usar contentable() — mantenido para compatibilidad con Pages antiguas */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(LegacyMedia::class, 'featured_media_id');
    }

    public function featuredFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'featured_media_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SectionItem::class, 'section_id')->orderBy('sort_order');
    }

    public function media(): MorphToMany
    {
        return $this->morphToMany(LegacyMedia::class, 'mediable', 'legacy_mediables', null, 'media_id')
            ->withPivot('collection', 'sort_order')
            ->orderByPivot('sort_order');
    }

    public function galleryFiles(): MorphToMany
    {
        return $this->morphToMany(MediaFile::class, 'attachable', 'media_attachments', null, 'media_file_id')
            ->withPivot('collection', 'sort_order')
            ->orderByPivot('sort_order');
    }
}
