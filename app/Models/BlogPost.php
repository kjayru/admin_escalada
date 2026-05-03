<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Slimani\MediaManager\Models\File as MediaFile;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'author_name',
        'excerpt',
        'body',
        'content_mode',
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

    public function sections(): MorphMany
    {
        return $this->morphMany(PageSection::class, 'contentable')->orderBy('sort_order');
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(LegacyMedia::class, 'featured_media_id');
    }

    /** Imagen destacada desde el nuevo sistema slimani/filament-media-manager */
    public function featuredFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'featured_media_id');
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
}
