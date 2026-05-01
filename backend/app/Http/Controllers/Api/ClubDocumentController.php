<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClubDocument;
use Illuminate\Http\JsonResponse;

class ClubDocumentController extends Controller
{
    public function index(): JsonResponse
    {
        $documents = ClubDocument::query()
            ->where('is_published', true)
            ->orderByDesc('uploaded_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (ClubDocument $document) {
                return [
                    'id' => $document->id,
                    'title' => $document->title,
                    'uploaded_at' => optional($document->uploaded_at)->format('Y-m-d'),
                    'file_url' => $document->file_url,
                    'file_extension' => $document->file_extension,
                    'file_size' => $document->formatted_file_size,
                ];
            });

        return response()->json([
            'data' => $documents,
        ]);
    }
}
