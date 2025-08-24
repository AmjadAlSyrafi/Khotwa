<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\EventImageRequest;
use App\Models\Event;
use App\Services\UploadImageService;

class EventImageController extends Controller
{
    public function __construct(private UploadImageService $uploader) {}

    public function updateCover(EventImageRequest $request, Event $event)
    {
        $path = $this->uploader->save(
            $request->file('image'),
            'images/events',
            $event->cover_image
        );

        $event->update(['cover_image' => $path]);

        return ApiResponse::success([
            'cover_image_url' => $event->cover_image_url,
        ], 'Event cover updated successfully.');
    }
}
