<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PageSectionResource;

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

    private function resolveFeaturedMedia(): array
    {
        // Preferir slimani/spatie (sistema nuevo) — el MediaPicker de Filament guarda su File ID
        if ($this->relationLoaded('featuredFile') && $this->featuredFile) {
            $spatieMedia = $this->featuredFile->getFirstMedia() ?? $this->featuredFile->getFirstMedia('default');
            $url = $spatieMedia?->getUrl();
            if ($url) {
                return [
                    'url' => $url,
                    'alt' => $this->featuredFile->alt_text ?? $this->title,
                ];
            }
        }

        // Fallback al sistema legacy si aún existe el archivo
        if ($this->relationLoaded('featuredMedia') && $this->featuredMedia) {
            return [
                'url' => $this->featuredMedia->url ?? self::picsum($this->slug, 1200, 630),
                'alt' => $this->featuredMedia->alt ?? $this->title,
            ];
        }

        return [
            'url' => self::picsum($this->slug, 1200, 630),
            'alt' => $this->title,
        ];
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
            'body'   => $this->when($this->content_mode !== 'blocks', $this->body),
            'content_mode' => $this->content_mode ?? 'classic',
            'sections' => $this->when(
                $this->content_mode === 'blocks' && $this->relationLoaded('sections'),
                PageSectionResource::collection($this->sections)
            ),
            'featured_media' => $this->resolveFeaturedMedia(),
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
