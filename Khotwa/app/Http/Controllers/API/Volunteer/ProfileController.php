<?php

namespace App\Http\Controllers\API\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Volunteer;
use App\Helpers\ApiResponse;
use App\Http\Resources\VolunteerResource;

class ProfileController extends Controller
{
    // عرض ملفي الشخصي
    public function show()
    {
        $volunteer = Volunteer::where('user_id', Auth::id())->first();
        if (!$volunteer) {
            return ApiResponse::error('Volunteer profile not found', 404);
        }
        return ApiResponse::success(new VolunteerResource($volunteer), 'Profile retrieved successfully');
    }

    // تعديل بيانات ملفي
    public function update(Request $request)
    {
        $volunteer = Volunteer::where('user_id', Auth::id())->first();
        if (!$volunteer) {
            return ApiResponse::error('Volunteer profile not found', 404);
        }

        $data = $request->validate([
            'full_name' => 'sometimes|string|max:100',
            'phone' => 'sometimes|string|max:20',
            'city' => 'nullable|string|max:100',
            'interests' => 'nullable|json',
            'availability' => 'nullable|json',
            'preferred_time' => 'nullable|string',
            'motivation' => 'nullable|string',
        ]);

        $volunteer->update($data);

        return ApiResponse::success(new VolunteerResource($volunteer), 'Profile updated successfully');
    }
}
