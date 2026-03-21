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
        $query = TransparencyDocument::published()
            ->with(['media'])
            ->orderBy('published_at', 'desc');

        if ($request->has('type')) {
            $query->byType($request->input('type'));
        }

        if ($request->has('year')) {
            $query->byYear((int) $request->input('year'));
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
            ->published()
            ->with(['media'])
            ->firstOrFail();

        return new TransparencyDocumentResource($document);
    }
}
