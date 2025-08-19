<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EventStatusService;

class UpdateEventStatus extends Command
{
    protected $signature = 'events:update-status';
    protected $description = 'Update event statuses based on current date';

    public function handle(EventStatusService $service): void
    {
        $service->updateStatuses();

        $this->info('Event statuses updated successfully.');
    }
}
