<?php

namespace App\Http\Controllers;

use App\Models\Warning;
use App\Http\Requests\StoreWarningRequest;
use App\Http\Requests\UpdateWarningRequest;
use App\Helpers\ApiResponse;
use App\Http\Resources\WarningResource;
use App\Http\Controllers\NotificationController;

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

        // إشعار المتطوع بأن الإنذار تم قبوله
        NotificationController::notifyVolunteerWarningAccepted($warning);

        // إشعار الأدمن بأن الإنذار تمت معالجته
        NotificationController::notifyAdminWarningProcessed($warning);

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
        $warning = Warning::with('volunteer.user')->find($id);
        if (!$warning) {
            return ApiResponse::error('Warning not found.', 404);
        }

        $warning->update(['status' => 'rejected']);

    // إشعار الأدمن بأن الإنذار تمت معالجته (رفض)
    NotificationController::notifyAdminWarningProcessed($warning);

        //  إرسال إشعار للمتطوع
        NotificationController::send(
            $warning->volunteer->user_id,
            "waring of you has been rejected: {$warning->event->title}",
            "because: {$warning->reason}"
        );

        return ApiResponse::success(
            new WarningResource($warning),
            'Warning rejected successfully.'
        );
    }

}
