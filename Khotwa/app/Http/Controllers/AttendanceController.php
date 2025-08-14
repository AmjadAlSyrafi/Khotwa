<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\BadgeService;

class AttendanceController extends Controller
{

public function __construct(private BadgeService $badgeService) {}

    /**
     * Volunteer QR Check-in
     */
    public function checkIn(StoreAttendanceRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        if ($data['checkin_method'] === 'QR') {
            $volunteer = $user->volunteer;
            if (!$volunteer) {
                return ApiResponse::error('Only volunteers can check in via QR.', 403);
            }

            $event = Event::where('qr_token', $data['qr_token'])
                ->where('qr_token_expires_at', '>=', now())
                ->where('status', 'open')
                ->first();

            if (!$event) {
                return ApiResponse::error('Invalid or expired QR token.', 400);
            }

            $isRegistered = $event->registrations()
                ->where('volunteer_id', $volunteer->id)
                ->exists();

            if (!$isRegistered) {
                return ApiResponse::error('You are not registered for this event.', 403);
            }

            if (Attendance::where('volunteer_id', $volunteer->id)->where('event_id', $event->id)->exists()) {
                return ApiResponse::error('You have already checked in for this event.', 409);
            }

            Attendance::create([
                'volunteer_id'   => $volunteer->id,
                'event_id'       => $event->id,
                'checkin_time'   => now(),
                'checkin_method' => 'QR',
            ]);

            $event->increment('current_volunteers');
            //Badges Service
            $this->badgeService->syncAfterAttendance($volunteer->id);
            return ApiResponse::success([], 'Check-in successful via QR.');
        }

        return ApiResponse::error('Manual check-in must be done via supervisor endpoint.', 400);
    }

    /**
     * Volunteer QR Check-out
     */
    public function checkOut(StoreAttendanceRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        if ($data['checkin_method'] === 'QR') {
            $volunteer = $user->volunteer;
            if (!$volunteer) {
                return ApiResponse::error('Only volunteers can check out via QR.', 403);
            }

            $event = Event::where('qr_token', $data['qr_token'])
                ->where('qr_token_expires_at', '>=', now())
                ->where('status', 'open')
                ->first();

            if (!$event) {
                return ApiResponse::error('Invalid or expired QR token.', 400);
            }

            $isRegistered = $event->registrations()
                ->where('volunteer_id', $volunteer->id)
                ->exists();

            if (!$isRegistered) {
                return ApiResponse::error('You are not registered for this event.', 403);
            }

            $attendance = Attendance::where('volunteer_id', $volunteer->id)
                ->where('event_id', $event->id)
                ->first();

            if (!$attendance) {
                return ApiResponse::error('You have not checked in for this event.', 404);
            }

            if ($attendance->checkout_time) {
                return ApiResponse::error('You have already checked out from this event.', 409);
            }

            $attendance->checkout_time = now();

            $hours = Carbon::parse($attendance->checkin_time)->diffInHours($attendance->checkout_time);
            if ($hours > 0) {
                $volunteer->increment('total_volunteer_hours', $hours);
            }

            $attendance->save();
            $event->decrement('current_volunteers');

            return ApiResponse::success([], 'Check-out successful via QR.');
        }

        return ApiResponse::error('Manual check-out must be done via supervisor endpoint.', 400);
    }

    /**
     * Manual check-in/check-out for multiple volunteers by Supervisor
     */
    public function manualAttendance(UpdateAttendanceRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        $event = Event::where('id', $data['event_id'])
            ->where('status', 'open')
            ->first();

        if (!$event) {
            return ApiResponse::error('Event not found or not open.', 404);
        }

        DB::beginTransaction();
        try {
            foreach ($data['volunteer_ids'] as $volunteerId) {
                $attendance = Attendance::where('volunteer_id', $volunteerId)
                    ->where('event_id', $event->id)
                    ->first();

                if ($data['action'] === 'checkin') {
                    if (!$attendance) {
                        Attendance::create([
                            'volunteer_id'   => $volunteerId,
                            'event_id'       => $event->id,
                            'checkin_time'   => now(),
                            'checkin_method' => 'Manual',
                            'supervisor_id'  => $user->id,
                        ]);
                        $event->increment('current_volunteers');
                        //Badges Service
                        $this->badgeService->syncAfterAttendance($volunteerId);
                    }
                }

                if ($data['action'] === 'checkout') {
                    if ($attendance && !$attendance->checkout_time) {
                        $attendance->checkout_time = now();

                        $hours = Carbon::parse($attendance->checkin_time)->diffInHours($attendance->checkout_time);
                        if ($hours > 0) {
                            $attendance->volunteer->increment('total_volunteer_hours', $hours);
                        }

                        $attendance->save();
                        $event->decrement('current_volunteers');
                    }
                }
            }

            DB::commit();
            return ApiResponse::success([], ucfirst($data['action']) . ' completed successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::error('Something went wrong: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Supervisor get event registrations (list of volunteers)
     */
    public function eventRegistrations($eventId)
    {
        $event = Event::with(['registrations.volunteer'])
            ->where('id', $eventId)
            ->first();

        if (!$event) {
            return ApiResponse::error('Event not found or you are not assigned to it.', 404);
        }

        return ApiResponse::success($event->registrations, 'Event registrations fetched successfully.');
    }

    public function eventAttendance($eventId)
    {
        $user = Auth::user();

        if ($user->role->name !== 'Supervisor') {
            return ApiResponse::error('Only supervisors can view event attendance.', 403);
        }

        $attendance = Attendance::where('event_id', $eventId)
            ->with('volunteer')
            ->get();

        return ApiResponse::success($attendance, 'Event attendance fetched successfully.');
    }

    /**
     * Volunteer attendance log
     */
    public function volunteerAttendanceLog()
    {
        $volunteer = Auth::user()->volunteer;

        if (!$volunteer) {
            return ApiResponse::error('Only volunteers can view their attendance log.', 403);
        }

        $attendance = Attendance::where('volunteer_id', $volunteer->id)
            ->with('event')
            ->get();

        return ApiResponse::success($attendance, 'Volunteer attendance log fetched successfully.');
    }
}
