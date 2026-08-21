<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\LeadAssignedEvent;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendLeadAssignmentNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Lead $lead,
        public User $assignedUser
    ) {}

    public function handle(): void
    {
        Log::info("Lead {$this->lead->id} assigned to user {$this->assignedUser->email}");

        // Trigger real-time WebSocket notification event
        event(new LeadAssignedEvent($this->lead, $this->assignedUser));
    }
}
