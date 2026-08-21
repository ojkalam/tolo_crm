<?php

declare(strict_types=1);

namespace App\Events;

use App\Http\Resources\DealResource;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DealStageUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Deal $deal,
        public string $previousStageId,
        public ?User $user = null
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('pipeline.'.$this->deal->pipeline_id),
            new PrivateChannel('organization.'.$this->deal->organization_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'deal.stage.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'deal' => (new DealResource($this->deal->loadMissing(['pipeline', 'stage', 'company', 'contact', 'assignedTo'])))->resolve(),
            'previous_stage_id' => $this->previousStageId,
            'new_stage_id' => $this->deal->stage_id,
            'updated_by' => $this->user?->id,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
