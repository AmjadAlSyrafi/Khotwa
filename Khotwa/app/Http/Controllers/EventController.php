<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Helpers\ApiResponse;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Storage;
use App\Services\RecommendationService;
use App\Http\Resources\EventResource;

class EventController extends Controller
{
    public function __construct(private RecommendationService $recommendationService) {}

    public function index()
    {
        $events = Event::with('project')
            ->orderBy('date', 'asc')
            ->get();

        return ApiResponse::success(EventResource::collection($events), 'Events fetched successfully');
    }

    public function show($id)
    {
        $event = Event::with('project')->find($id);
        if (!$event) {
            return ApiResponse::error('Event not found', 404);
        }
        return ApiResponse::success(new EventResource($event), 'Event details fetched successfully');
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        $data['current_volunteers'] = 0;

        $event = Event::create($data);

        // Generate and save QR token on event creation
        $event->qr_token = (string) Str::uuid();
        $event->qr_token_expires_at = Carbon::parse($event->end_date)->endOfDay();
        $event->save();

        return ApiResponse::success(new EventResource($event), 'Event created successfully', 201);
    }

    public function update(UpdateEventRequest $request, $id)
    {
        $event = Event::find($id);
        if (!$event) {
            return ApiResponse::error('Event not found', 404);
        }

        $data = $request->validated();

        if (isset($data['required_volunteers']) && $data['required_volunteers'] < $event->current_volunteers) {
            return ApiResponse::error('Required volunteers cannot be less than current volunteers.', 422);
        }

        $event->update($data);

        return ApiResponse::success(new EventResource($event), 'Event updated successfully');
    }

    public function destroy($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return ApiResponse::error('Event not found', 404);
        }

        $event->delete();
        return ApiResponse::success(null, 'Event deleted successfully');
    }

    public function recommended()
    {
        $volunteer = optional(Auth::user())->volunteer;

        if (!$volunteer) {
            return ApiResponse::error('Only volunteers can access recommendations.', 403);
        }

        $events = $this->recommendationService->getRecommendedEvents($volunteer);

        return ApiResponse::success(EventResource::collection($events), 'Personalized recommended events fetched successfully.');
    }

    public function volunteerEvents()
    {
        if (!Auth::check() || !Auth::user()->volunteer) {
            return ApiResponse::error('Only volunteers can access their events.', 403);
        }

        $volunteer = Auth::user()->volunteer;

        $events = Event::whereHas('registrations', function ($query) use ($volunteer) {
                $query->where('volunteer_id', $volunteer->id)
                    ->whereNotIn('status', ['withdrawn', 'rejected']);
            })
            ->where('status', 'open')
            ->with('project')
            ->orderBy('date', 'asc')
            ->get();

        return ApiResponse::success(EventResource::collection($events), 'Volunteer events fetched successfully.');
    }

    public function supervisorEvents()
    {
        if (!Auth::check() || Auth::user()->role->name !== 'Supervisor') {
            return ApiResponse::error('Only supervisors can access supervised events.', 403);
        }

        $events = Event::where('supervisor_id', Auth::id())
            ->where('status', 'open')
            ->with(['project', 'registrations.volunteer'])
            ->orderBy('date', 'asc')
            ->get();

        return ApiResponse::success(EventResource::collection($events), 'Supervisor events fetched successfully.');
    }

    public function volunteerEventLog()
    {
        if (!Auth::check() || !Auth::user()->volunteer) {
            return ApiResponse::error('Only volunteers can access their event log.', 403);
        }

        $volunteer = Auth::user()->volunteer;

        $events = Event::whereHas('registrations', function ($query) use ($volunteer) {
                $query->where('volunteer_id', $volunteer->id)
                    ->whereIn('status', ['accepted', 'withdrawn', 'rejected']);
            })
            ->where('status', 'completed')
            ->with(['project', 'registrations' => function ($query) use ($volunteer) {
                $query->where('volunteer_id', $volunteer->id);
            }])
            ->orderBy('date', 'desc')
            ->get();

        return ApiResponse::success(EventResource::collection($events), 'Volunteer event log fetched successfully.');
    }

    public function supervisorEventLog()
    {
        if (!Auth::check() || Auth::user()->role->name !== 'Supervisor') {
            return ApiResponse::error('Only supervisors can access their event log.', 403);
        }

        $events = Event::where('supervisor_id', Auth::id())
            ->where('status', 'completed')
            ->with(['project', 'registrations.volunteer'])
            ->orderBy('date', 'desc')
            ->get();

        return ApiResponse::success(EventResource::collection($events), 'Supervisor event log fetched successfully.');
    }

    public function generateQrCode($eventId)
    {
        $user = Auth::user();
        if ($user->role->name !== 'Supervisor') {
            return ApiResponse::error('Only supervisors can generate QR codes.', 403);
        }

        $event = Event::find($eventId);
        if (!$event) {
            return ApiResponse::error('Event not found.', 404);
        }

        if (!$event->qr_token || now()->greaterThan($event->qr_token_expires_at)) {
            return ApiResponse::error('QR token expired or not available.', 400);
        }

        // Check if a QR code image path is already stored in the database
        if ($event->qr_image_path && Storage::disk('public')->exists($event->qr_image_path)) {
            return response()->file(storage_path("app/public/{$event->qr_image_path}"));
        }

        // If not, generate QR using endroid/qr-code
        $fileName = "qr_codes/event_{$event->id}.png";
        $url = $event->qr_token;

        $result = Builder::create()
            ->data($url)
            ->size(300)
            ->margin(10)
            ->build();

        Storage::disk('public')->put($fileName, $result->getString());

        $event->qr_image_path = $fileName;
        $event->save();

        return response()->file(storage_path("app/public/{$fileName}"));
    }
}
