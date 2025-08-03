<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Volunteer;
use App\Helpers\ApiResponse;

class VolunteerAdminController extends Controller
{
    // عرض كل المتطوعين
    public function index()
    {
        $volunteers = Volunteer::with('user')->get();
        return ApiResponse::success($volunteers, 'Volunteers retrieved successfully');
    }

    // عرض متطوع معين
    public function show($id)
    {
        $volunteer = Volunteer::with('user')->find($id);
        if (!$volunteer) {
            return ApiResponse::error('Volunteer not found', 404);
        }
        return ApiResponse::success($volunteer, 'Volunteer retrieved successfully');
    }

    // إضافة متطوع
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:volunteers,email',
            'phone' => 'required|string|max:20',
            'city' => 'nullable|string|max:100',
            'status' => 'required|string',
        ]);

        $volunteer = Volunteer::create($request->all());
        return ApiResponse::success($volunteer, 'Volunteer created successfully', 201);
    }

    // تغيير بيانات متطوع
    public function update(Request $request, $id)
    {
        $volunteer = Volunteer::find($id);
        if (!$volunteer) {
            return ApiResponse::error('Volunteer not found', 404);
        }

        $data = $request->validate([
            'full_name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:volunteers,email,' . $volunteer->id,
            'phone' => 'sometimes|string|max:20',
            'city' => 'nullable|string|max:100',
            'status' => 'sometimes|string',
        ]);

        $volunteer->update($request->all());
        return ApiResponse::success($volunteer, 'Volunteer updated successfully');
    }

    // حذف متطوع
    public function destroy($id)
    {
        $volunteer = Volunteer::find($id);
        if (!$volunteer) {
            return ApiResponse::error('Volunteer not found', 404);
        }

        $volunteer->delete();
        return ApiResponse::success(null, 'Volunteer deleted successfully');
    }
}
