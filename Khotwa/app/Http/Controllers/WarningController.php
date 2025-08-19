<?php

namespace App\Http\Controllers;

use App\Models\Warning;
use App\Http\Requests\StoreWarningRequest;
use App\Http\Requests\UpdateWarningRequest;
use App\Helpers\ApiResponse;
use App\Http\Resources\WarningResource;

class WarningController extends Controller
{
    /**
     * Admin: list all pending warnings
     */
    public function index()
    {
        $warnings = Warning::with(['volunteer', 'supervisor', 'event'])
            ->where('status', 'pending')
            ->get();

        return ApiResponse::success(
            WarningResource::collection($warnings),
            'Pending warnings fetched successfully.'
        );
    }

    /**
     * Admin: approve a warning
     */
    public function approve($id)
    {
        $warning = Warning::find($id);
        if (!$warning) {
            return ApiResponse::error('Warning not found.', 404);
        }

        $warning->update(['status' => 'approved']);

        return ApiResponse::success(
            new WarningResource($warning),
            'Warning approved successfully.'
        );
    }

    /**
     * Admin: reject a warning
     */
    public function reject($id)
    {
        $warning = Warning::find($id);
        if (!$warning) {
            return ApiResponse::error('Warning not found.', 404);
        }

        $warning->update(['status' => 'rejected']);

        return ApiResponse::success(
            new WarningResource($warning),
            'Warning rejected successfully.'
        );
    }
}
