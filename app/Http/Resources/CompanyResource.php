<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Company
 */
class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'owner_id' => $this->owner_id,
            'name' => $this->name,
            'domain' => $this->domain,
            'industry' => $this->industry,
            'annual_revenue' => $this->annual_revenue !== null ? (float) $this->annual_revenue : null,
            'employees_count' => $this->employees_count,
            'phone' => $this->phone,
            'website' => $this->website,
            'address_street' => $this->address_street,
            'address_city' => $this->address_city,
            'address_state' => $this->address_state,
            'address_country' => $this->address_country,
            'address_postal_code' => $this->address_postal_code,
            'custom_attributes' => $this->custom_attributes ?? [],
            'owner' => new UserResource($this->whenLoaded('owner')),
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
            'contacts_count' => $this->whenCounted('contacts'),
            'deals_count' => $this->whenCounted('deals'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
