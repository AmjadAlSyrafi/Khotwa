<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ProjectImageRequest;
use App\Models\Project;
use App\Services\UploadImageService;

class ProjectImageController extends Controller
{
    public function __construct(private UploadImageService $uploader) {}

    public function updateCover(ProjectImageRequest $request, Project $project)
    {
        $path = $this->uploader->save(
            $request->file('image'),
            'images/projects',
            $project->cover_image
        );

        $project->update(['cover_image' => $path]);

        return ApiResponse::success([
            'cover_image_url' => $project->cover_image_url,
        ], 'Project cover updated successfully.');
    }
}
