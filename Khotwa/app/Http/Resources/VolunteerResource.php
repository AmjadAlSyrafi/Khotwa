<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'full_name'          => $this->full_name,
            'email'              => $this->email,
            'phone'              => $this->phone,
            'city_id'            => $this->city_id,
            'education_level'    => $this->education_level,
            'university'         => $this->university,
            'registration_date'  => $this->registration_date?->toDateString(),
            'total_volunteer_hours' => $this->total_volunteer_hours,
            'profile_image_url'  => $this->profile_image_url,
            'skills'             => $this->skills->pluck('name'),
            'badges'             => $this->badges->pluck('name'),
        ];
    }
}
