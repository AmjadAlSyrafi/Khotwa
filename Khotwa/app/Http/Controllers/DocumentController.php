<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * List all documents.
     */
    public function index()
    {
        $documents = Document::with(['uploader','volunteer','event','project'])
            ->latest()
            ->get();

        return ApiResponse::success(DocumentResource::collection($documents));
    }

    /**
     * Store a new document.
     */
    public function store(StoreDocumentRequest $request)
    {
        $originalName = $request->file('file')->getClientOriginalName();

        $filePath = $request->file('file')->storeAs('documents', $originalName, 'local');
        $document = Document::create([
            'file_path'    => $filePath,
            'file_name'    => $originalName,
            'type'         => $request->input('type'),
            'uploaded_by'  => Auth::user()->id,
            'volunteer_id' => $request->input('volunteer_id'),
            'event_id'     => $request->input('event_id'),
            'project_id'   => $request->input('project_id'),
        ]);

        return ApiResponse::success(new DocumentResource($document), 'Document uploaded successfully.', 201);
    }

    /**
     * Show a document.
     */
    public function show($id)
    {
        $document = Document::with(['uploader','volunteer','event','project'])->find($id);

        if (!$document) {
            return ApiResponse::error('Document not found.', 404);
        }

        return ApiResponse::success(new DocumentResource($document));
    }

    /**
     * Delete a document.
     */
    public function destroy($id)
    {
        $document = Document::find($id);

        if (!$document) {
            return ApiResponse::error('Document not found.', 404);
        }

        Storage::delete($document->file_path);
        $document->delete();

        return ApiResponse::success(null, 'Document deleted successfully.');
    }

    public function download(Request $request, $id)
    {

        if (! $request->hasValidSignature()) {
            abort(401, 'Invalid or expired link.');
        }

        $document = Document::findOrFail($id);

        if (!Storage::disk('local')->exists($document->file_path)) {
            return ApiResponse::error('File not found.', 404);
        }

        return Storage::disk('local')->download($document->file_path,$document->file_name);
    }


}
