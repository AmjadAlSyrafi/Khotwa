<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'volunteer_id' => $this->volunteer_id,
            'volunteer_name' => $this->volunteer?->full_name,
            'score' => $this->score,
            'last_updated' => $this->last_updated,
        ];
    }
}
