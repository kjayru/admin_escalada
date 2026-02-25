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
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'featured_media' => new MediaResource($this->whenLoaded('featuredMedia')),
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
