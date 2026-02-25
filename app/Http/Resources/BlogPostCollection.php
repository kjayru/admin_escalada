<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BlogPostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt,
                    'featured_media' => $post->featuredMedia ? [
                        'url' => $post->featuredMedia->url,
                        'alt' => $post->featuredMedia->alt,
                    ] : null,
                    'published_at' => $post->published_at?->toISOString(),
                    'comments_count' => $post->approved_comments_count ?? 0,
                ];
            }),
        ];
    }
}
