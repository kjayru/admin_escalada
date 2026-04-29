<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Slimani\MediaManager\Models\File as MediaFile;

class Timeline extends Model
{
    protected $fillable = [
        'date',
        'title',
        'body',
        'year',
        'month',
        'media_id',
        'status',
        'published_at',
        'order',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'order' => 'integer',
        'published_at' => 'datetime',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(LegacyMedia::class);
    }

    public function featuredFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'media_id');
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
