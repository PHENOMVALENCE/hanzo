<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function download(Document $document): Response
    {
        $this->authorize('view', $document);

        if (! Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        $filename = basename($document->file_path);

        return Storage::disk('private')->response(
            $document->file_path,
            $filename,
            [
                'Content-Type' => Storage::disk('private')->mimeType($document->file_path),
            ]
        );
    }
}
