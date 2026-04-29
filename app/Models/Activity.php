<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Activity extends Model
{
    protected $fillable = [
        'name',
        'year',
        'media_id',
        'pdf_path',
        'status',
        'published_at',
        'order',
    ];

    protected $casts = [
        'year' => 'integer',
        'order' => 'integer',
        'published_at' => 'datetime',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(LegacyMedia::class);
    }

    public function getPdfUrlAttribute(): ?string
    {
        if (!$this->pdf_path) {
            return null;
        }
        return Storage::disk('public')->url($this->pdf_path);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at');
    }

    public function scopeByYear($query, int $year)
    {
        return $query->where('year', $year);
    }
}
