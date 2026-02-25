<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class SectionItem extends Model
{
    protected $fillable = [
        'section_id',
        'title',
        'body',
        'link_url',
        'link_label',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'section_id');
    }

    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable', 'mediables')
            ->withPivot('collection', 'sort_order')
            ->orderBy('sort_order');
    }
}
