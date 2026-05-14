<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Slimani\MediaManager\Models\File as MediaFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'category_id',
        'user_id',
        'name',
        'slug',
        'summary',
        'description',
        'price',
        'currency',
        'featured_media_id',
        'status',
        'gallery_items', // Virtual field for Repeater (legacy)
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saved(function (Product $product) {
            // Handle gallery_items from Repeater
            if (isset($product->attributes['gallery_items']) && is_array($product->attributes['gallery_items'])) {
                $fileIds = collect($product->attributes['gallery_items'])
                    ->pluck('file_id')
                    ->filter()
                    ->values();
                
                $syncData = $fileIds->mapWithKeys(fn($id, $index) => [$id => ['sort_order' => $index]])->toArray();
                $product->galleryFiles()->sync($syncData);
                unset($product->attributes['gallery_items']);
            }
            // Handle gallery_ids from MediaPicker multiple
            elseif (isset($product->attributes['gallery_ids']) && is_array($product->attributes['gallery_ids'])) {
                $galleryIds = $product->attributes['gallery_ids'];
                $syncData = collect($galleryIds)->mapWithKeys(fn($id, $index) => [$id => ['sort_order' => $index]])->toArray();
                $product->galleryFiles()->sync($syncData);
                unset($product->attributes['gallery_ids']);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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

    /** Galería de imágenes del producto (nueva tabla pivot) */
    public function galleryFiles(): BelongsToMany
    {
        return $this->belongsToMany(MediaFile::class, 'product_gallery', 'product_id', 'file_id')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('sort_order');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(ProductInquiry::class);
    }

    public function legacyMedia(): MorphToMany
    {
        return $this->morphToMany(LegacyMedia::class, 'mediable', 'legacy_mediables', 'mediable_id', 'media_id')
            ->withPivot('collection', 'sort_order')
            ->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
