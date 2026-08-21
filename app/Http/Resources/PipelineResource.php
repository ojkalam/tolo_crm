<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Pipeline;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Pipeline
 */
class PipelineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'name' => $this->name,
            'is_default' => (bool) $this->is_default,
            'stages' => PipelineStageResource::collection($this->whenLoaded('stages')),
            'deals_count' => $this->whenCounted('deals'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
