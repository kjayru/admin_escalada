<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class PageSection extends Model
{
    protected $fillable = [
        'page_id',
        'type',
        'sort_order',
        'heading',
        'subheading',
        'body',
        'settings',
        'status',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SectionItem::class, 'section_id')->orderBy('sort_order');
    }

    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable', 'mediables')
            ->withPivot('collection', 'sort_order')
            ->orderBy('sort_order');
    }
}
