<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Helpers\ApiResponse;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('project')->get();
        return ApiResponse::success($events, 'Events fetched successfully');
    }

    public function show($id)
    {
        $event = Event::with('project')->find($id);
        if (!$event) {
            return ApiResponse::error('Event not found', 404);
        }
        return ApiResponse::success($event, 'Event details fetched successfully');
    }

    public function store(StoreEventRequest $request)
    {
        $request['registered_count'] = 0;
        $event = Event::create($request->validated());
        return ApiResponse::success($event, 'Event created successfully', 201);
    }

    public function update(StoreEventRequest $request, $id)
    {
        $event = Event::find($id);
        if (!$event) {
            return ApiResponse::error('Event not found', 404);
        }

        $event->update($request->validated());
        return ApiResponse::success($event, 'Event updated successfully');
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
}

