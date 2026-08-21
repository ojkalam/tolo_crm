<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Deal
 */
class DealResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'pipeline_id' => $this->pipeline_id,
            'stage_id' => $this->stage_id,
            'company_id' => $this->company_id,
            'contact_id' => $this->contact_id,
            'assigned_to' => $this->assigned_to,
            'name' => $this->name,
            'amount' => $this->amount !== null ? (float) $this->amount : 0.0,
            'currency' => $this->currency ?? 'USD',
            'expected_close_date' => $this->expected_close_date?->toDateString(),
            'status' => $this->status,
            'custom_attributes' => $this->custom_attributes ?? [],
            'pipeline' => new PipelineResource($this->whenLoaded('pipeline')),
            'stage' => new PipelineStageResource($this->whenLoaded('stage')),
            'company' => new CompanyResource($this->whenLoaded('company')),
            'contact' => new ContactResource($this->whenLoaded('contact')),
            'assigned_user' => new UserResource($this->whenLoaded('assignedTo')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
