<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransparencyDocumentResource;
use App\Models\TransparencyDocument;
use Illuminate\Http\Request;

class TransparencyDocumentController extends Controller
{
    /**
     * Display a listing of published transparency documents.
     */
    public function index(Request $request)
    {
        $query = TransparencyDocument::where('is_published', true)
            ->with(['file'])
            ->orderBy('published_at', 'desc');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('year')) {
            $query->whereYear('published_at', $request->year);
        }

        $documents = $query->paginate($request->input('per_page', 20));

        return TransparencyDocumentResource::collection($documents);
    }

    /**
     * Display the specified transparency document.
     */
    public function show(string $slug)
    {
        $document = TransparencyDocument::where('slug', $slug)
            ->where('is_published', true)
            ->with(['file'])
            ->firstOrFail();

        return new TransparencyDocumentResource($document);
    }
}
