<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Contact
 */
class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'company_id' => $this->company_id,
            'assigned_user_id' => $this->assigned_user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'job_title' => $this->job_title,
            'department' => $this->department,
            'lifecycle_stage' => $this->lifecycle_stage,
            'custom_attributes' => $this->custom_attributes ?? [],
            'company' => new CompanyResource($this->whenLoaded('company')),
            'assigned_user' => new UserResource($this->whenLoaded('assignedUser')),
            'deals_count' => $this->whenCounted('deals'),
            'activities_count' => $this->whenCounted('activities'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
