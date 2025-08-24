<?php

namespace App\Http\Controllers\API\Volunteer;

use App\Helpers\ApiResponse;
use App\Http\Requests\VolunteerImageRequest;
use App\Services\UploadImageService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class VolunteerProfileController extends Controller
{
    public function __construct(private UploadImageService $uploader) {}

    /**
     * Volunteer updates their own profile image.
     */
    public function updateImage(VolunteerImageRequest $request)
    {
        $volunteer = Auth::user()->volunteer;
        if (!$volunteer) {
            return ApiResponse::error('Only volunteers can update profile image.', 403);
        }

        $path = $this->uploader->save(
            $request->file('image'),
            'images/volunteers',
            $volunteer->profile_image
        );

        $volunteer->update(['profile_image' => $path]);

        return ApiResponse::success([
            'profile_image_url' => $volunteer->profile_image_url,
        ], 'Profile image updated successfully.');
    }
}
