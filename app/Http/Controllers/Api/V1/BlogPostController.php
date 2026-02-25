<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostCollection;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogPostController extends Controller
{
    /**
     * Display a listing of published blog posts.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $query = BlogPost::where('status', 'published')
            ->with(['featuredMedia'])
            ->withCount('approvedComments')
            ->orderBy('published_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate($perPage);

        return new BlogPostCollection($posts);
    }

    /**
     * Display the specified blog post.
     */
    public function show(string $slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('status', 'published')
            ->with(['featuredMedia', 'approvedComments', 'media'])
            ->firstOrFail();

        return new BlogPostResource($post);
    }

    /**
     * Store a new comment for a blog post.
     */
    public function storeComment(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'comment' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $post = BlogPost::findOrFail($id);

        $comment = BlogComment::create([
            'post_id' => $post->id,
            'name' => $request->name,
            'email' => $request->email,
            'comment' => $request->comment,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Comentario enviado. Está pendiente de moderación.',
            'data' => [
                'id' => $comment->id,
                'status' => $comment->status,
            ]
        ], 201);
    }
}
