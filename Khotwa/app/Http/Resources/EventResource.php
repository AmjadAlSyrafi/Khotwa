<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'time' => $this->time,
            'duration_hours' => $this->duration_hours,
            'location' => $this->location,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'status' => $this->status,
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
            'required_volunteers' => $this->required_volunteers,
            'current_volunteers' => $this->current_volunteers,
            'registered_count' => $this->registrations->count(),
            'project_id' => $this->project_id,
            'project_name' => $this->project->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'qr_token' => $this->qr_token,
            'qr_token_expires_at' => $this->qr_token_expires_at,
            'qr_image_path' => $this->qr_image_path ? asset('storage/' . $this->qr_image_path) : null,

        ];
    }
}
