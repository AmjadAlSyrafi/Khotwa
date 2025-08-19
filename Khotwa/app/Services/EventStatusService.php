<?php

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;

class EventStatusService
{
    public function updateStatuses(): void
    {
        $now = Carbon::now();

        Event::where('status', 'upcoming')
            ->whereRaw("STR_TO_DATE(CONCAT(`date`, ' ', `time`), '%Y-%m-%d %H:%i:%s') <= ?", [$now])
            ->update(['status' => 'open']);
    }
}
