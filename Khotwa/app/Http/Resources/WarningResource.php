<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WarningResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'volunteer'    => [
                'id'   => $this->volunteer->id ?? null,
                'name' => $this->volunteer->full_name ?? null
            ],
            'event'        => [
                'id'    => $this->event->id ?? null,
                'title' => $this->event->title ?? null
            ],
            'supervisor'   => [
                'id'   => $this->supervisor->id ?? null,
                'name' => $this->supervisor->username ?? null
            ],
            'reason'       => $this->reason,
            'status'       => $this->status,
            'created_at'   => $this->created_at->toDateTimeString(),
        ];
    }
}
