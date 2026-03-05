<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Order;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Order $order)
    {
        $this->authorize('view', $order);

        $documents = $order->documents()->with('uploadedBy')->get();

        return view('buyer.orders.documents', compact('order', 'documents'));
    }

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
