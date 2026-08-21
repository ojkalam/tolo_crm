<?php

declare(strict_types=1);

namespace App\Events;

use App\Http\Resources\LeadResource;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadAssignedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Lead $lead,
        public User $assignedUser
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->assignedUser->id),
            new PrivateChannel('organization.' . $this->lead->organization_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'lead.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'lead' => (new LeadResource($this->lead->loadMissing('assignedUser')))->resolve(),
            'assigned_to' => [
                'id' => $this->assignedUser->id,
                'name' => $this->assignedUser->name,
            ],
            'message' => "New Lead Assigned: {$this->lead->name}",
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
