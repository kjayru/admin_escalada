<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransparencyDocument extends Model
{
    protected $fillable = [
        'title',
        'year',
        'type',
        'description',
        'media_id',
        'published_at',
        'status',
    ];

    protected $casts = [
        'year' => 'integer',
        'published_at' => 'datetime',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
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

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
