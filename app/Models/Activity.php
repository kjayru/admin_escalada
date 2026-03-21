<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'name',
        'year',
        'media_id',
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
}
