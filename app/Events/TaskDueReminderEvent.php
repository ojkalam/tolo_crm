<?php

declare(strict_types=1);

namespace App\Events;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskDueReminderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Activity $activity,
        public User $user
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->user->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.due.reminder';
    }

    public function broadcastWith(): array
    {
        return [
            'activity' => (new ActivityResource($this->activity))->resolve(),
            'message' => "Task Due Reminder: {$this->activity->title}",
            'due_date' => $this->activity->due_date?->toIso8601String(),
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
