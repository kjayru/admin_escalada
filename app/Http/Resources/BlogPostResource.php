<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    private static function picsum(string $seed, int $w, int $h): string
    {
        return "https://picsum.photos/seed/{$seed}/{$w}/{$h}";
    }

    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'title'  => $this->title,
            'slug'   => $this->slug,
            'category' => $this->category,
            'author' => ['name' => $this->author_name ?? 'Escalada Libre'],
            'excerpt' => $this->excerpt,
            'body'   => $this->body,
            'featured_media' => $this->whenLoaded('featuredMedia', function () {
                return [
                    'url' => $this->featuredMedia?->url ?? self::picsum($this->slug, 1200, 630),
                    'alt' => $this->featuredMedia?->alt ?? $this->title,
                ];
            }, [
                'url' => self::picsum($this->slug, 1200, 630),
                'alt' => $this->title,
            ]),
            'media' => MediaResource::collection($this->whenLoaded('media')),
            'published_at' => $this->published_at?->toISOString(),
            'comments' => $this->when(
                $this->relationLoaded('approvedComments'),
                function () {
                    return $this->approvedComments->map(function ($comment) {
                        return [
                            'id' => $comment->id,
                            'name' => $comment->name,
                            'comment' => $comment->comment,
                            'created_at' => $comment->created_at->toISOString(),
                        ];
                    });
                }
            ),
            'comments_count' => $this->when(
                isset($this->approved_comments_count),
                $this->approved_comments_count
            ),
        ];
    }
}
