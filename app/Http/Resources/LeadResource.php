<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Lead
 */
class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'assigned_user_id' => $this->assigned_user_id,
            'title' => $this->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->name,
            'company_name' => $this->company_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status?->value ?? $this->status,
            'source' => $this->source?->value ?? $this->source,
            'score' => $this->score,
            'estimated_value' => $this->estimated_value !== null ? (float) $this->estimated_value : 0.0,
            'notes' => $this->notes,
            'custom_attributes' => $this->custom_attributes ?? [],
            'converted_at' => $this->converted_at?->toIso8601String(),
            'assigned_user' => new UserResource($this->whenLoaded('assignedUser')),
            'activities_count' => $this->whenCounted('activities'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
