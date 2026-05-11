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
        'mobile_image_id',
        'status',
        'featured_settings_data', // virtual: Filament lo redirige a settings via mutador
        'map_settings_data',      // virtual: igual para tipo 'map'
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

    public function mobileImage(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'mobile_image_id');
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

    /**
     * Mutador virtual para Filament: recibe los settings del bloque "featured"
     * (number, tag, link_url, image_position) y los guarda en la columna settings.
     * No genera columna en la BD — solo redirige al atributo settings.
     */
    public function setFeaturedSettingsDataAttribute(array $value): void
    {
        $this->settings = $value;
    }

    /**
     * Mutador virtual para Filament: recibe los labels del bloque "map"
     * (key, label_1, label_2, label_3) y los guarda en la columna settings.
     * No genera columna en la BD — solo redirige al atributo settings.
     */
    public function setMapSettingsDataAttribute(array $value): void
    {
        $existing = $this->settings ?? [];
        $this->settings = array_merge($existing, array_filter($value, fn($v) => $v !== null && $v !== ''));
    }
}
