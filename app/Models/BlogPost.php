<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'author_name',
        'excerpt',
        'body',
        'status',
        'is_featured',
        'published_at',
        'featured_media_id',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $appends = ['author'];

    public function getAuthorAttribute(): array
    {
        return ['name' => $this->author_name ?? 'Escalada Libre'];
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_media_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'post_id');
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'post_id')
            ->where('status', 'approved')
            ->latest();
    }

    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable', 'mediables')
            ->withPivot('collection', 'sort_order')
            ->orderBy('sort_order');
    }
}
