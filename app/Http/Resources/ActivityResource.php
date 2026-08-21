<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Activity
 */
class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'user_id' => $this->user_id,
            'subjectable_type' => class_basename($this->subjectable_type),
            'subjectable_id' => $this->subjectable_id,
            'type' => $this->type?->value ?? $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'is_completed' => $this->completed_at !== null,
            'metadata' => $this->metadata ?? [],
            'user' => new UserResource($this->whenLoaded('user')),
            'subject' => $this->whenLoaded('subjectable'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
