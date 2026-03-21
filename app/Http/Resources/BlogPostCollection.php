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
    private static function picsum(string $seed, int $w, int $h): string
    {
        return "https://picsum.photos/seed/{$seed}/{$w}/{$h}";
    }

    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($post) {
            return [
                'id'            => $post->id,
                'title'         => $post->title,
                'slug'          => $post->slug,
                'category'      => $post->category,
                'excerpt'       => $post->excerpt,
                'author'        => ['name' => $post->author_name ?? 'Escalada Libre'],
                'featured_media' => [
                    'url' => $post->featuredMedia?->url ?? self::picsum($post->slug, 1200, 630),
                    'alt' => $post->featuredMedia?->alt ?? $post->title,
                ],
                'is_featured'    => (bool) $post->is_featured,
                'published_at'  => $post->published_at?->toISOString(),
                'comments_count' => $post->approved_comments_count ?? 0,
            ];
        })->toArray();
    }
}
